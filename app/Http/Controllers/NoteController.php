<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Composition;
use App\Models\Devoir;
use App\Models\Etablissement;
use App\Models\Etudiant;
use App\Models\EvaluationType;
use App\Models\Examen;
use App\Models\Matiere;
use App\Models\Note;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use ZipArchive;

class NoteController extends Controller
{
    // ─── Saisie examens ──────────────────────────────────────────────────────

    public function saisir(Examen $examen)
    {
        $etudiants = $examen->classe->etudiants()
            ->where('statut', 'actif')->orderBy('nom')->get();

        $notes = Note::where('examen_id', $examen->id)->pluck('note', 'etudiant_id');

        return view('notes.saisir', compact('examen', 'etudiants', 'notes'));
    }

    public function enregistrer(Request $request, Examen $examen)
    {
        $request->validate([
            'notes'   => 'required|array',
            'notes.*' => 'nullable|numeric|min:0|max:' . $examen->note_max,
        ]);

        foreach ($request->notes as $etudiantId => $note) {
            if ($note !== null && $note !== '') {
                Note::updateOrCreate(
                    ['examen_id' => $examen->id, 'etudiant_id' => $etudiantId, 'type' => 'examen'],
                    ['note' => $note]
                );
            }
        }

        return redirect()->route('examens.show', $examen)
            ->with('succes', 'Notes enregistrées avec succès.');
    }

    // ─── Saisie devoirs ───────────────────────────────────────────────────────

    public function saisirDevoir(Devoir $devoir)
    {
        $etudiants = $devoir->classe->etudiants()
            ->where('statut', 'actif')->orderBy('nom')->get();

        $notes = Note::where('devoir_id', $devoir->id)->pluck('note', 'etudiant_id');

        return view('notes.saisir', compact('devoir', 'etudiants', 'notes'));
    }

    public function enregistrerDevoir(Request $request, Devoir $devoir)
    {
        $request->validate([
            'notes'   => 'required|array',
            'notes.*' => 'nullable|numeric|min:0|max:' . $devoir->note_max,
        ]);

        foreach ($request->notes as $etudiantId => $note) {
            if ($note !== null && $note !== '') {
                Note::updateOrCreate(
                    ['devoir_id' => $devoir->id, 'etudiant_id' => $etudiantId, 'type' => 'devoir'],
                    ['note' => $note]
                );
            }
        }

        return redirect()->route('devoirs.show', $devoir)
            ->with('succes', 'Notes enregistrées avec succès.');
    }

    // ─── Saisie compositions ──────────────────────────────────────────────────

    public function saisirComposition(Composition $composition)
    {
        $etudiants = $composition->classe->etudiants()
            ->where('statut', 'actif')->orderBy('nom')->get();

        $notes = Note::where('composition_id', $composition->id)->pluck('note', 'etudiant_id');

        return view('notes.saisir', compact('composition', 'etudiants', 'notes'));
    }

    public function enregistrerComposition(Request $request, Composition $composition)
    {
        $request->validate([
            'notes'   => 'required|array',
            'notes.*' => 'nullable|numeric|min:0|max:' . $composition->note_max,
        ]);

        foreach ($request->notes as $etudiantId => $note) {
            if ($note !== null && $note !== '') {
                Note::updateOrCreate(
                    ['composition_id' => $composition->id, 'etudiant_id' => $etudiantId, 'type' => 'composition'],
                    ['note' => $note]
                );
            }
        }

        return redirect()->route('compositions.show', $composition)
            ->with('succes', 'Notes enregistrées avec succès.');
    }

    // ─── Calcul bulletin pondéré ──────────────────────────────────────────────

    /**
     * Charge les notes et calcule les moyennes pondérées par matière.
     *
     * Algorithme :
     *   1. Récupérer les matières de la classe
     *   2. Pour chaque matière → moyenneEtudiant() (pondérée par EvaluationType.poids)
     *   3. Moyenne générale = Σ(moy_matière × coeff) / Σ(coeff)
     *   4. Rang calculé par comparaison avec les camarades
     */
    private function chargerNotesBulletin(Etudiant $etudiant, string $anneeScolaire, string $trimestre): array
    {
        $isElementaire = in_array($etudiant->classe?->categorie, ['elementaire', 'prescolaire']);

        // Élémentaire : compositions uniquement (pas de devoirs ni examens)
        $notesExamen = collect();
        $notesDevoir = collect();

        if (!$isElementaire) {
            $notesExamen = Note::where('etudiant_id', $etudiant->id)
                ->where('type', 'examen')
                ->whereHas('examen', fn($q) => $q->where('annee_scolaire', $anneeScolaire)
                    ->where('trimestre', $trimestre)->where('classe_id', $etudiant->classe_id))
                ->with(['examen.matiere'])->get()
                ->groupBy('examen.matiere.nom');

            $notesDevoir = Note::where('etudiant_id', $etudiant->id)
                ->where('type', 'devoir')
                ->whereHas('devoir', fn($q) => $q->where('annee_scolaire', $anneeScolaire)
                    ->where('trimestre', $trimestre)->where('classe_id', $etudiant->classe_id))
                ->with(['devoir.matiere'])->get()
                ->groupBy('devoir.matiere.nom');
        }

        $notesComposition = Note::where('etudiant_id', $etudiant->id)
            ->where('type', 'composition')
            ->whereHas('composition', fn($q) => $q->where('annee_scolaire', $anneeScolaire)
                ->where('trimestre', $trimestre)->where('classe_id', $etudiant->classe_id))
            ->with(['composition.matiere'])->get()
            ->groupBy('composition.matiere.nom');

        // Calcul pondéré par matière
        $matieres         = Matiere::where('classe_id', $etudiant->classe_id)->get();
        $lignesMatiere    = [];
        $totalPoints      = 0.0;
        $totalCoefficient = 0.0;

        foreach ($matieres as $matiere) {
            $moyComp = $this->avgNotesType($etudiant->id, 'composition', $matiere->id, $anneeScolaire, $trimestre);

            if ($isElementaire) {
                // Élémentaire : la moyenne matière = note de composition
                $moyExamen = null;
                $moyDevoir = null;

                if ($moyComp === null) {
                    continue;
                }

                $moyMatiere = $moyComp;
            } else {
                // Secondaire : moyenne pondérée devoir + examen + composition
                $moyExamen = $this->avgNotesType($etudiant->id, 'examen', $matiere->id, $anneeScolaire, $trimestre);
                $moyDevoir = $this->avgNotesType($etudiant->id, 'devoir', $matiere->id, $anneeScolaire, $trimestre);

                if ($moyExamen === null && $moyDevoir === null && $moyComp === null) {
                    continue;
                }

                $moyMatiere = $matiere->moyenneEtudiant($etudiant->id, $anneeScolaire, $trimestre);
            }

            $coefficient = (float) ($matiere->coefficient ?? 1);
            $points      = round($moyMatiere * $coefficient, 2);

            $lignesMatiere[] = [
                'matiere'         => $matiere,
                'coefficient'     => $coefficient,
                'moyenne_examen'  => $moyExamen,
                'moyenne_devoir'  => $moyDevoir,
                'moyenne_compo'   => $moyComp,
                'moyenne_matiere' => $moyMatiere,
                'points'          => $points,
                'mention'         => $this->mention($moyMatiere),
                'appreciation'    => $this->appreciation($moyMatiere),
            ];

            $totalPoints      += $points;
            $totalCoefficient += $coefficient;
        }

        $moyennePonderee = $totalCoefficient > 0
            ? round($totalPoints / $totalCoefficient, 2)
            : 0.0;

        // Ancienne moyenne simple (fallback compatibilité)
        $toutes  = $notesExamen->flatten()->merge($notesDevoir->flatten())->merge($notesComposition->flatten());
        $moyenne = round($toutes->avg('note') ?? 0, 2);

        $rang = $this->calculerRang($etudiant, $anneeScolaire, $trimestre, $moyennePonderee);

        return compact(
            'notesExamen', 'notesDevoir', 'notesComposition',
            'moyenne', 'lignesMatiere', 'moyennePonderee',
            'totalCoefficient', 'rang'
        );
    }

    /** Moyenne des notes d'un type pour une matière/période donnée. */
    private function avgNotesType(int $etudiantId, string $type, int $matiereId, string $annee, string $trim): ?float
    {
        $notes = Note::where('etudiant_id', $etudiantId)
            ->where('type', $type)
            ->whereHas($type, fn($q) => $q->where('matiere_id', $matiereId)
                ->where('annee_scolaire', $annee)->where('trimestre', $trim))
            ->pluck('note');

        return $notes->isNotEmpty() ? round((float) $notes->avg(), 2) : null;
    }

    /** Rang de l'étudiant dans sa classe basé sur la moyenne pondérée. */
    private function calculerRang(Etudiant $etudiant, string $annee, string $trimestre, float $moyenneEtudiant): int
    {
        $matieres     = Matiere::where('classe_id', $etudiant->classe_id)->get();
        $matiereIds   = $matieres->pluck('id');
        $camaradesIds = Etudiant::where('classe_id', $etudiant->classe_id)
            ->where('statut', 'actif')->where('id', '!=', $etudiant->id)->pluck('id');

        if ($camaradesIds->isEmpty()) return 1;

        // 3 requêtes globales au lieu de N×M requêtes individuelles
        $notesComp = Note::whereIn('etudiant_id', $camaradesIds)->where('type', 'composition')
            ->whereHas('composition', fn($q) => $q->whereIn('matiere_id', $matiereIds)
                ->where('annee_scolaire', $annee)->where('trimestre', $trimestre))
            ->with('composition:id,matiere_id')->get();

        $notesExam = Note::whereIn('etudiant_id', $camaradesIds)->where('type', 'examen')
            ->whereHas('examen', fn($q) => $q->whereIn('matiere_id', $matiereIds)
                ->where('annee_scolaire', $annee)->where('trimestre', $trimestre))
            ->with('examen:id,matiere_id')->get();

        $notesDevoir = Note::whereIn('etudiant_id', $camaradesIds)->where('type', 'devoir')
            ->whereHas('devoir', fn($q) => $q->whereIn('matiere_id', $matiereIds)
                ->where('annee_scolaire', $annee)->where('trimestre', $trimestre))
            ->with('devoir:id,matiere_id')->get();

        // Indexer : [etudiant_id][matiere_id][type] = [notes]
        $index = [];
        foreach ($notesComp   as $n) { if ($n->composition) $index[$n->etudiant_id][$n->composition->matiere_id]['composition'][] = $n->note; }
        foreach ($notesExam   as $n) { if ($n->examen)      $index[$n->etudiant_id][$n->examen->matiere_id]['examen'][]           = $n->note; }
        foreach ($notesDevoir as $n) { if ($n->devoir)       $index[$n->etudiant_id][$n->devoir->matiere_id]['devoir'][]          = $n->note; }

        $evalTypes = EvaluationType::all()->keyBy('slug');
        $rang = 1;

        foreach ($camaradesIds as $cId) {
            $totalPts   = 0.0;
            $totalCoeff = 0.0;

            foreach ($matieres as $m) {
                $notesParType = $index[$cId][$m->id] ?? [];
                if (empty($notesParType)) continue;

                if ($evalTypes->isNotEmpty()) {
                    $pts = 0.0; $poids = 0.0;
                    foreach ($notesParType as $slug => $vals) {
                        $p     = (float) ($evalTypes->get($slug)?->poids ?? 1.0);
                        $pts   += (array_sum($vals) / count($vals)) * $p;
                        $poids += $p;
                    }
                    $moy = $poids > 0 ? $pts / $poids : 0.0;
                } else {
                    $allVals = array_merge(...array_values($notesParType));
                    $moy     = count($allVals) > 0 ? array_sum($allVals) / count($allVals) : 0.0;
                }

                $coeff = (float) ($m->coefficient ?? 1);
                if ($moy > 0) { $totalPts += $moy * $coeff; $totalCoeff += $coeff; }
            }

            if ($totalCoeff > 0 && ($totalPts / $totalCoeff) > $moyenneEtudiant) {
                $rang++;
            }
        }

        return $rang;
    }

    private function mention(float $note): string
    {
        if ($note >= 16) return 'Très Bien';
        if ($note >= 14) return 'Bien';
        if ($note >= 12) return 'Assez Bien';
        if ($note >= 10) return 'Passable';
        return 'Insuffisant';
    }

    private function appreciation(float $note): string
    {
        if ($note >= 16) return 'Excellent travail !';
        if ($note >= 14) return 'Bon travail.';
        if ($note >= 12) return 'Travail satisfaisant.';
        if ($note >= 10) return 'Peut mieux faire.';
        return 'Des efforts sont nécessaires.';
    }

    // ─── Vues du bulletin ────────────────────────────────────────────────────

    public function bulletin(Etudiant $etudiant, Request $request)
    {
        $etudiant->load('classe.responsable');
        $anneeScolaire = $request->annee_scolaire ?? AnneeScolaire::libelleActif();

        // Pour les classes élémentaires/préscolaires → semestres (S1/S2/S3)
        $isElementaire = in_array($etudiant->classe?->categorie, ['elementaire', 'prescolaire']);
        if ($request->trimestre) {
            $trimestre = $request->trimestre;
        } elseif ($isElementaire) {
            // Convertir T1→S1, T2→S2, T3→S3 par défaut
            $ta = AnneeScolaire::trimestreActif();
            $trimestre = str_replace('T', 'S', $ta);
        } else {
            $trimestre = AnneeScolaire::trimestreActif();
        }

        $etablissement = Etablissement::first();

        extract($this->chargerNotesBulletin($etudiant, $anneeScolaire, $trimestre));

        return view('notes.bulletin', compact(
            'etudiant', 'notesExamen', 'notesDevoir', 'notesComposition',
            'moyenne', 'lignesMatiere', 'moyennePonderee', 'totalCoefficient',
            'rang', 'anneeScolaire', 'trimestre', 'etablissement'
        ));
    }

    public function bulletinPdf(Etudiant $etudiant, Request $request)
    {
        $etudiant->load('classe.responsable');
        $anneeScolaire = $request->annee_scolaire ?? AnneeScolaire::libelleActif();

        $isElementaire = in_array($etudiant->classe?->categorie, ['elementaire', 'prescolaire']);
        if ($request->trimestre) {
            $trimestre = $request->trimestre;
        } elseif ($isElementaire) {
            $trimestre = str_replace('T', 'S', AnneeScolaire::trimestreActif());
        } else {
            $trimestre = AnneeScolaire::trimestreActif();
        }

        $etablissement = Etablissement::first();

        extract($this->chargerNotesBulletin($etudiant, $anneeScolaire, $trimestre));

        $pdf = Pdf::loadView('bulletins.pdf.bulletin', compact(
            'etudiant', 'notesExamen', 'notesDevoir', 'notesComposition',
            'moyenne', 'lignesMatiere', 'moyennePonderee', 'totalCoefficient',
            'rang', 'anneeScolaire', 'trimestre', 'etablissement'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('bulletin_' . $etudiant->matricule . '_' . $trimestre . '.pdf');
    }

    public function bulletinPrint(Etudiant $etudiant, Request $request)
    {
        $etudiant->load('classe.responsable');
        $anneeScolaire = $request->annee_scolaire ?? AnneeScolaire::libelleActif();

        $isElementaire = in_array($etudiant->classe?->categorie, ['elementaire', 'prescolaire']);
        if ($request->trimestre) {
            $trimestre = $request->trimestre;
        } elseif ($isElementaire) {
            $trimestre = str_replace('T', 'S', AnneeScolaire::trimestreActif());
        } else {
            $trimestre = AnneeScolaire::trimestreActif();
        }

        $etablissement = Etablissement::first();

        extract($this->chargerNotesBulletin($etudiant, $anneeScolaire, $trimestre));

        $pdf = Pdf::loadView('bulletins.pdf.bulletin', compact(
            'etudiant', 'notesExamen', 'notesDevoir', 'notesComposition',
            'moyenne', 'lignesMatiere', 'moyennePonderee', 'totalCoefficient',
            'rang', 'anneeScolaire', 'trimestre', 'etablissement'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('bulletin_' . $etudiant->matricule . '_' . $trimestre . '.pdf');
    }

    public function certificat(Etudiant $etudiant)
    {
        $etablissement = Etablissement::first();
        $pdf = Pdf::loadView('bulletins.pdf.certificat', compact('etudiant', 'etablissement'));
        return $pdf->download('certificat_' . $etudiant->matricule . '.pdf');
    }

    public function downloadAllBulletins(Request $request)
    {
        $anneeScolaire = $request->annee_scolaire ?? AnneeScolaire::libelleActif();
        $etablissement = Etablissement::first();
        $classeId      = $request->classe_id;

        // Récupérer les classes concernées
        $classesQuery = Classe::whereHas('etudiants', fn($q) => $q->where('statut', 'actif'));
        if ($classeId) {
            $classesQuery->where('id', $classeId);
        }
        $classes = $classesQuery->with('responsable')->orderBy('nom')->get();

        // ── Si une seule classe → un seul PDF direct ──
        if ($classes->count() === 1) {
            $classe = $classes->first();
            $trimestre = $this->resolveTrimestre($request->trimestre, $classe);

            $bulletins = $this->genererBulletinsClasse($classe, $anneeScolaire, $trimestre);

            $pdf = Pdf::loadView('bulletins.pdf.bulletin-classe', compact(
                'bulletins', 'classe', 'anneeScolaire', 'trimestre', 'etablissement'
            ))->setPaper('a4', 'portrait');

            return $pdf->download('bulletins_' . $classe->nom . '_' . $trimestre . '.pdf');
        }

        // ── Plusieurs classes → un ZIP avec un PDF par classe ──
        $zip     = new ZipArchive();
        $zipPath = storage_path('app/bulletins_' . time() . '.zip');

        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            foreach ($classes as $classe) {
                $trimestre = $this->resolveTrimestre($request->trimestre, $classe);

                $bulletins = $this->genererBulletinsClasse($classe, $anneeScolaire, $trimestre);
                if (empty($bulletins)) continue;

                $pdf = Pdf::loadView('bulletins.pdf.bulletin-classe', compact(
                    'bulletins', 'classe', 'anneeScolaire', 'trimestre', 'etablissement'
                ))->setPaper('a4', 'portrait');

                $nomFichier = 'bulletins_' . str_replace(' ', '_', $classe->nom) . '_' . $trimestre . '.pdf';
                $zip->addFromString($nomFichier, $pdf->output());
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Génère les données de bulletin pour tous les élèves d'une classe.
     */
    private function genererBulletinsClasse(Classe $classe, string $anneeScolaire, string $trimestre): array
    {
        $etudiants = Etudiant::where('classe_id', $classe->id)
            ->where('statut', 'actif')
            ->orderBy('nom')
            ->get();

        $bulletins = [];
        foreach ($etudiants as $etudiant) {
            $data = $this->chargerNotesBulletin($etudiant, $anneeScolaire, $trimestre);
            $data['etudiant'] = $etudiant;
            $bulletins[] = $data;
        }

        // Trier par rang (moyenne pondérée décroissante = rang croissant)
        usort($bulletins, function ($a, $b) {
            return ($a['rang'] ?? 999) <=> ($b['rang'] ?? 999);
        });

        return $bulletins;
    }

    /**
     * Résout le trimestre/semestre selon la catégorie de la classe.
     */
    private function resolveTrimestre(?string $requestTrimestre, Classe $classe): string
    {
        $isElementaire = in_array($classe->categorie, ['elementaire', 'prescolaire']);

        if ($requestTrimestre) {
            // Si élémentaire et trimestre T reçu → convertir en S
            if ($isElementaire && str_starts_with($requestTrimestre, 'T')) {
                return str_replace('T', 'S', $requestTrimestre);
            }
            return $requestTrimestre;
        }

        $ta = AnneeScolaire::trimestreActif();
        return $isElementaire ? str_replace('T', 'S', $ta) : $ta;
    }
}
