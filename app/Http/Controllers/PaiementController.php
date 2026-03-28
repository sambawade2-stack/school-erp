<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Etablissement;
use App\Models\Etudiant;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Tarif;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        $query = Paiement::with('etudiant');

        if ($request->filled('recherche')) {
            $s = $request->recherche;
            $query->whereHas('etudiant', fn($q) =>
                $q->where('nom', 'like', "%{$s}%")->orWhere('prenom', 'like', "%{$s}%")
            )->orWhere('numero_recu', 'like', "%{$s}%");
        }

        if ($request->filled('type')) {
            if ($request->type === 'groupe_inscription') {
                $query->whereIn('type_paiement', Tarif::GROUPE_INSCRIPTION);
            } else {
                $query->where('type_paiement', $request->type);
            }
        }

        if ($request->filled('mois')) {
            $query->whereMonth('date_paiement', $request->mois)
                  ->whereYear('date_paiement', $request->annee ?? now()->year);
        }

        $paiements = $query->orderByDesc('date_paiement')->paginate(20)->withQueryString();

        // Construire la requête de stats selon les filtres actifs
        $statsQuery = Paiement::query();
        $moisFiltre = $request->filled('mois') ? (int) $request->mois : now()->month;
        $anneeFiltre = $request->filled('annee') ? (int) $request->annee : now()->year;

        $statsQuery->whereMonth('date_paiement', $moisFiltre)
                   ->whereYear('date_paiement', $anneeFiltre);

        $totalFiltre = (clone $statsQuery)->sum('montant');
        $totalAnnee  = Paiement::whereYear('date_paiement', $anneeFiltre)->sum('montant');

        $totauxParType = (clone $statsQuery)
            ->selectRaw("type_paiement, SUM(montant) as total")
            ->groupBy('type_paiement')
            ->pluck('total', 'type_paiement');

        // Regrouper les 4 types d'inscription en une seule carte
        $groupeInscription = Tarif::GROUPE_INSCRIPTION;
        $totalInscription  = $totauxParType->only($groupeInscription)->sum();

        // Types affichés individuellement (hors inscription groupée)
        $typesFrais = collect(Tarif::TYPES)->except($groupeInscription)->toArray();
        $typeColors = Tarif::TYPE_COLORS;

        return view('paiements.index', compact(
            'paiements', 'totalFiltre', 'totalAnnee', 'totauxParType',
            'moisFiltre', 'anneeFiltre', 'typesFrais', 'typeColors',
            'totalInscription'
        ));
    }

    public function create(Request $request)
    {
        $anneeActive = AnneeScolaire::active();
        $etudiant = $request->filled('etudiant_id')
            ? Etudiant::find($request->etudiant_id)
            : null;
        $etudiants = Etudiant::where('statut', 'actif')->orderBy('nom')->with('inscriptions')->get();

        // Map etudiant_id => niveau (from latest inscription for active year)
        $niveauxEtudiants = $etudiants->mapWithKeys(function ($e) use ($anneeActive) {
            $inscription = $e->inscriptions
                ->when($anneeActive, fn($c) => $c->where('annee_scolaire', $anneeActive->libelle))
                ->sortByDesc('created_at')->first();
            return [$e->id => $inscription?->niveau];
        });

        // All tarifs for active year, keyed by niveau
        $tarifs = $anneeActive
            ? Tarif::where('annee_scolaire', $anneeActive->libelle)->get()->groupBy('niveau')
            : collect();

        // Trimestre courant par défaut (depuis l'année scolaire active)
        $trimestreDefaut = $anneeActive?->trimestre_actuel ?? 'T1';

        // Pré-remplissage depuis un paiement partiel existant
        $prefill = [
            'type_paiement'   => $request->type_paiement,
            'trimestre'       => $request->trimestre,
            'montant_restant' => $request->montant_restant ? (float) $request->montant_restant : null,
        ];

        return view('paiements.create', compact('etudiants', 'etudiant', 'niveauxEtudiants', 'tarifs', 'anneeActive', 'trimestreDefaut', 'prefill'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id'    => 'required|exists:etudiants,id',
            'annee_scolaire' => 'required|string',
        ]);

        // Mode multi-paiement (depuis la grille tarifaire)
        if ($request->filled('paiements_json')) {
            $paiements = json_decode($request->paiements_json, true);

            if (!is_array($paiements) || empty($paiements)) {
                return back()->withErrors(['paiements_json' => 'Aucun paiement sélectionné.']);
            }

            // Un seul numéro de facture pour tout le groupe
            $numeroFacture = $this->genererNumeroRecu();

            $paiementsCreated = [];
            foreach ($paiements as $p) {
                $montantTotal = (float) ($p['montant_total'] ?? 0);
                $montant = (float) ($p['montant'] ?? 0);

                if ($montant <= 0) continue;

                $paiementsCreated[] = Paiement::create([
                    'etudiant_id'    => $request->etudiant_id,
                    'montant_total'  => $montantTotal,
                    'montant'        => $montant,
                    'type_paiement'  => $p['type_paiement'],
                    'date_paiement'  => $p['date_paiement'] ?? now()->toDateString(),
                    'trimestre'      => $p['trimestre'] ?? null,
                    'annee_scolaire' => $request->annee_scolaire,
                    'numero_recu'    => $this->genererNumeroRecu(),
                    'numero_facture' => $numeroFacture,
                    'statut'         => $montant >= $montantTotal ? 'complet' : 'partiel',
                    'lignes'         => $p['lignes'] ?? null,
                    'remarque'       => $request->remarque,
                ]);
            }

            if (empty($paiementsCreated)) {
                return back()->withErrors(['paiements_json' => 'Aucun paiement valide.']);
            }

            // Stocker les IDs en session pour le téléchargement du reçu groupé
            $ids = collect($paiementsCreated)->pluck('id')->toArray();
            session(['recu_groupe_ids' => $ids]);

            return redirect()->route('paiements.index')
                ->with('succes', count($paiementsCreated) . ' paiement(s) enregistré(s) — Facture N° ' . $numeroFacture)
                ->with('recu_groupe_ids', $ids);
        }

        // Mode simple (saisie manuelle)
        $request->validate([
            'montant_total'  => 'required|numeric|min:0.01',
            'montant'        => 'required|numeric|min:0.01',
            'type_paiement'  => 'required|string',
            'date_paiement'  => 'required|date',
            'trimestre'      => 'nullable|in:T1,T2,T3,S1,S2,S3',
        ]);

        $data = $request->only(['etudiant_id', 'montant_total', 'montant', 'type_paiement', 'date_paiement', 'trimestre', 'annee_scolaire', 'remarque']);
        $data['numero_recu'] = $this->genererNumeroRecu();
        $data['statut'] = (float)$data['montant'] >= (float)$data['montant_total'] ? 'complet' : 'partiel';

        Paiement::create($data);

        // Si ce paiement solde un partiel antérieur, mettre à jour son statut
        $this->solderPartielsAntérieurs(
            (int)   $data['etudiant_id'],
            $data['type_paiement'],
            $data['trimestre'] ?? null,
            $data['annee_scolaire']
        );

        return redirect()->route('paiements.index')
            ->with('succes', 'Paiement enregistré. Reçu N° ' . $data['numero_recu']);
    }

    /**
     * Génère le PDF groupé pour plusieurs paiements (depuis la session ou les IDs en query string).
     */
    public function recuGroupe(Request $request)
    {
        // Chargement par numéro de facture (depuis la liste)
        if ($request->filled('facture')) {
            $paiementsCreated = Paiement::with('etudiant.classe')
                ->where('numero_facture', $request->facture)
                ->get();
        } else {
            $ids = $request->input('ids', session('recu_groupe_ids', []));
            if (empty($ids)) {
                abort(404, 'Aucun reçu groupé disponible.');
            }
            $paiementsCreated = Paiement::with('etudiant.classe')->whereIn('id', $ids)->get();
        }

        if ($paiementsCreated->isEmpty()) {
            abort(404);
        }

        $etudiant      = $paiementsCreated->first()->etudiant;
        $etablissement = Etablissement::first();

        $premierNumero = $paiementsCreated->first()->numero_recu;
        $dernierNumero = $paiementsCreated->last()->numero_recu;
        $filename = $paiementsCreated->count() > 1
            ? 'recus_' . $premierNumero . '_' . $dernierNumero . '.pdf'
            : 'recu_' . $premierNumero . '.pdf';

        $pdf = Pdf::loadView('paiements.pdf.recu-multiple', compact('paiementsCreated', 'etudiant', 'etablissement'));

        if ($request->boolean('preview')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    public function show(Paiement $paiement)
    {
        $paiement->load('etudiant.classe', 'tranches');
        $etablissement = Etablissement::first();
        return view('paiements.show', compact('paiement', 'etablissement'));
    }

    public function edit(Paiement $paiement)
    {
        $etudiants = Etudiant::where('statut', 'actif')->orderBy('nom')->get();
        return view('paiements.edit', compact('paiement', 'etudiants'));
    }

    public function update(Request $request, Paiement $paiement)
    {
        $data = $request->validate([
            'etudiant_id'    => 'required|exists:etudiants,id',
            'montant'        => 'required|numeric|min:0.01',
            'type_paiement'  => 'required|in:' . implode(',', array_keys(Tarif::TYPES)),
            'date_paiement'  => 'required|date',
            'annee_scolaire' => 'required|string',
            'trimestre'      => 'nullable|in:T1,T2,T3,S1,S2,S3',
            'remarque'       => 'nullable|string',
        ]);

        $paiement->update($data);

        return redirect()->route('paiements.show', $paiement)
            ->with('succes', 'Paiement modifié avec succès.');
    }

    public function destroy(Paiement $paiement)
    {
        $paiement->delete();
        return redirect()->route('paiements.index')->with('succes', 'Paiement supprimé.');
    }

    public function pdf(Paiement $paiement)
    {
        $paiement->load('etudiant.classe');
        $etablissement = Etablissement::first();
        $pdf = Pdf::loadView('paiements.pdf.recu', compact('paiement', 'etablissement'));
        return $pdf->download('recu_' . $paiement->numero_recu . '.pdf');
    }

    public function exportPdf(Request $request)
    {
        $query = Paiement::with('etudiant');
        $mois = $request->filled('mois') ? (int) $request->mois : now()->month;
        $annee = $request->filled('annee') ? (int) $request->annee : now()->year;

        $query->whereMonth('date_paiement', $mois)->whereYear('date_paiement', $annee);

        if ($request->filled('type')) {
            $query->where('type_paiement', $request->type);
        }

        $paiements = $query->orderByDesc('date_paiement')->get();
        $total = $paiements->sum('montant');
        $etablissement = Etablissement::first();
        $periode = \Carbon\Carbon::create($annee, $mois, 1)->locale('fr')->translatedFormat('F Y');

        $pdf = Pdf::loadView('exports.paiements-pdf', compact('paiements', 'total', 'etablissement', 'periode'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('paiements_' . $mois . '_' . $annee . '.pdf');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Paiement::with('etudiant');
        $mois = $request->filled('mois') ? (int) $request->mois : now()->month;
        $annee = $request->filled('annee') ? (int) $request->annee : now()->year;

        $query->whereMonth('date_paiement', $mois)->whereYear('date_paiement', $annee);

        if ($request->filled('type')) {
            $query->where('type_paiement', $request->type);
        }

        $paiements = $query->orderByDesc('date_paiement')->get();

        return response()->streamDownload(function () use ($paiements) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['N° Reçu', 'Élève', 'Classe', 'Type', 'Montant', 'Date', 'Trimestre', 'Statut'], ';');
            foreach ($paiements as $p) {
                fputcsv($handle, [
                    $p->numero_recu, $p->etudiant->nom_complet, $p->etudiant->classe?->nom ?? '—',
                    ucfirst($p->type_paiement), $p->montant, $p->date_paiement->format('d/m/Y'),
                    $p->trimestre ?? '—', ucfirst($p->statut),
                ], ';');
            }
            fclose($handle);
        }, 'paiements_' . $mois . '_' . $annee . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function genererNumeroRecu(): string
    {
        $dernier = Paiement::max('id') ?? 0;
        return 'REC-' . now()->year . '-' . str_pad($dernier + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Après un nouveau paiement, vérifie si la somme totale versée
     * pour ce même élève / type / trimestre / année couvre le montant dû.
     * Si oui, passe tous les partiels correspondants en "complet".
     */
    private function solderPartielsAntérieurs(int $etudiantId, string $type, ?string $trimestre, string $annee): void
    {
        $partiels = Paiement::where('etudiant_id', $etudiantId)
            ->where('type_paiement', $type)
            ->where('annee_scolaire', $annee)
            ->where('statut', 'partiel')
            ->when($trimestre, fn($q) => $q->where('trimestre', $trimestre))
            ->get();

        if ($partiels->isEmpty()) {
            return;
        }

        // Somme de tous les versements (partiels + complets) pour ce groupe
        $totalVerse = Paiement::where('etudiant_id', $etudiantId)
            ->where('type_paiement', $type)
            ->where('annee_scolaire', $annee)
            ->when($trimestre, fn($q) => $q->where('trimestre', $trimestre))
            ->sum('montant');

        // Référence : montant total dû du premier partiel trouvé
        $montantDu = (float) $partiels->first()->montant_total;

        if ($montantDu > 0 && (float) $totalVerse >= $montantDu) {
            Paiement::where('etudiant_id', $etudiantId)
                ->where('type_paiement', $type)
                ->where('annee_scolaire', $annee)
                ->where('statut', 'partiel')
                ->when($trimestre, fn($q) => $q->where('trimestre', $trimestre))
                ->update(['statut' => 'complet']);
        }
    }
}
