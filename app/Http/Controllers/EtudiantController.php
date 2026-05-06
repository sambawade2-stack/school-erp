<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Etudiant;
use App\Models\Inscription;
use App\Models\Etablissement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EtudiantController extends Controller
{
    /**
     * Regles de validation partagees entre store et update.
     */
    private function reglesValidation(bool $isUpdate = false): array
    {
        return [
            'prenom'           => 'required|string|max:100',
            'nom'              => 'required|string|max:100',
            'sexe'             => 'required|in:masculin,feminin',
            'date_naissance'   => 'nullable|date|before:today',
            'classe_id'        => 'nullable|integer|exists:classes,id',
            'telephone'        => ['nullable', 'string', 'max:20', 'regex:/^[\d\s\+\-\(\)\.]+$/'],
            'tel_parent'       => ['nullable', 'string', 'max:20', 'regex:/^[\d\s\+\-\(\)\.]+$/'],
            'nom_parent'       => 'nullable|string|max:100',
            'adresse'          => 'nullable|string|max:500',
            'date_inscription' => 'required|date',
            'statut'           => $isUpdate ? 'sometimes|in:actif,inactif,archive' : 'prohibited',
            'regime_paiement'  => 'sometimes|in:plein_tarif,demi_tarif',
            'photo'            => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'photo_webcam'     => ['nullable', 'string', 'regex:/^data:image\/(jpeg|jpg|png);base64,[A-Za-z0-9+\/]+=*$/'],
        ];
    }

    /**
     * Champs autorises (whitelist) pour eviter le mass assignment.
     */
    private function champsAutorises(bool $isUpdate = false): array
    {
        $champs = [
            'prenom', 'nom', 'sexe', 'date_naissance', 'classe_id',
            'telephone', 'adresse', 'nom_parent', 'tel_parent', 'date_inscription',
            'regime_paiement',
        ];
        if ($isUpdate) {
            $champs[] = 'statut';
        }
        return $champs;
    }

    public function index(Request $request)
    {
        $query = Etudiant::with('classe');

        if ($request->filled('recherche')) {
            $s = $request->recherche;
            $query->where(function ($q) use ($s) {
                $q->where('nom', 'like', "%{$s}%")
                  ->orWhere('prenom', 'like', "%{$s}%")
                  ->orWhere('matricule', 'like', "%{$s}%")
                  ->orWhere('telephone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('classe_id')) {
            if ($request->classe_id === 'sans_classe') {
                $query->whereNull('classe_id');
            } else {
                $query->where('classe_id', (int) $request->classe_id);
            }
        }

        if ($request->filled('statut') && in_array($request->statut, ['actif', 'inactif', 'archive'])) {
            $query->where('statut', $request->statut);
        } else {
            // Par defaut, masquer les archives
            $query->where('statut', '!=', 'archive');
        }

        $etudiants = $query->orderBy('nom')->paginate(20)->withQueryString();
        $classes   = Classe::orderBy('nom')->get();

        return view('etudiants.index', compact('etudiants', 'classes'));
    }

    public function create()
    {
        $classes       = Classe::orderBy('nom')->get();
        $anneeCourante = AnneeScolaire::active();
        return view('etudiants.create', compact('classes', 'anneeCourante'));
    }

    public function store(Request $request)
    {
        $request->validate($this->reglesValidation(false), [
            'telephone.regex'  => 'Le telephone ne doit contenir que des chiffres, espaces, +, - ou parentheses.',
            'tel_parent.regex' => 'Le telephone parent ne doit contenir que des chiffres, espaces, +, - ou parentheses.',
            'photo_webcam.regex' => 'La photo webcam est invalide.',
        ]);

        $photoNom = null;

        return DB::transaction(function () use ($request, &$photoNom) {
            // Preparer les donnees avec whitelist (BUG-002)
            $data = $request->only($this->champsAutorises(false));

            // Trim des champs texte (BUG-021)
            $data['prenom'] = trim($data['prenom']);
            $data['nom']    = trim($data['nom']);
            if (isset($data['nom_parent'])) {
                $data['nom_parent'] = trim($data['nom_parent']);
            }

            // Generer un matricule unique (BUG-001)
            $data['matricule'] = $this->genererMatricule();

            // Sauvegarder la photo (BUG-015 : dans la transaction)
            if ($request->hasFile('photo')) {
                $photoNom = $this->sauvegarderPhoto($request->file('photo'));
                $data['photo'] = $photoNom;
            } elseif ($request->filled('photo_webcam')) {
                $photoNom = $this->sauvegarderPhotoBase64($request->photo_webcam);
                $data['photo'] = $photoNom;
            }

            $etudiant = Etudiant::create($data);

            // Inscription automatique seulement si une annee scolaire active existe (BUG-014)
            $anneeActive = AnneeScolaire::active();
            $anneeLibelle = $request->annee_scolaire_inscription ?? $anneeActive?->libelle;

            if ($anneeLibelle) {
                Inscription::firstOrCreate([
                    'etudiant_id'    => $etudiant->id,
                    'annee_scolaire' => $anneeLibelle,
                ], [
                    'classe_id' => $etudiant->classe_id,
                    'niveau'    => $request->niveau ?? $etudiant->classe?->categorie,
                ]);
            }

            return redirect()->route('etudiants.show', $etudiant)
                ->with('succes', 'Etudiant ajouté' . ($anneeLibelle ? " et inscrit pour {$anneeLibelle}" : '') . '.');
        });
    }

    public function show(Etudiant $etudiant)
    {
        $etudiant->load([
            'classe', 'presences', 'inscriptions',
            'paiements' => fn($q) => $q->latest('date_paiement'),
            'notes'     => fn($q) => $q->latest()->take(10),
            'notes.examen.matiere',
            'notes.devoir.matiere',
            'notes.composition.matiere',
        ]);
        return view('etudiants.show', compact('etudiant'));
    }

    public function edit(Etudiant $etudiant)
    {
        $classes = Classe::orderBy('nom')->get();
        return view('etudiants.edit', compact('etudiant', 'classes'));
    }

    public function update(Request $request, Etudiant $etudiant)
    {
        $request->validate($this->reglesValidation(true), [
            'telephone.regex'  => 'Le telephone ne doit contenir que des chiffres, espaces, +, - ou parentheses.',
            'tel_parent.regex' => 'Le telephone parent ne doit contenir que des chiffres, espaces, +, - ou parentheses.',
            'photo_webcam.regex' => 'La photo webcam est invalide.',
        ]);

        return DB::transaction(function () use ($request, $etudiant) {
            // Whitelist des champs (BUG-002)
            $data = $request->only($this->champsAutorises(true));

            // Trim (BUG-021)
            $data['prenom'] = trim($data['prenom']);
            $data['nom']    = trim($data['nom']);
            if (isset($data['nom_parent'])) {
                $data['nom_parent'] = trim($data['nom_parent']);
            }

            // Gestion photo avec suppression correcte (BUG-005)
            if ($request->hasFile('photo')) {
                $this->supprimerPhoto($etudiant->photo);
                $data['photo'] = $this->sauvegarderPhoto($request->file('photo'));
            } elseif ($request->filled('photo_webcam')) {
                $this->supprimerPhoto($etudiant->photo);
                $data['photo'] = $this->sauvegarderPhotoBase64($request->photo_webcam);
            }

            $etudiant->update($data);

            // Synchroniser l'inscription de l'annee en cours (BUG-004)
            $anneeLibelle = AnneeScolaire::active()?->libelle;
            if ($anneeLibelle) {
                Inscription::where('etudiant_id', $etudiant->id)
                    ->where('annee_scolaire', $anneeLibelle)
                    ->update([
                        'classe_id' => $etudiant->classe_id,
                        'niveau'    => $request->niveau ?? $etudiant->classe?->categorie,
                    ]);
            }

            return redirect()->route('etudiants.show', $etudiant)
                ->with('succes', 'Etudiant modifié avec succès.');
        });
    }

    /**
     * Archiver un etudiant (ne jamais supprimer pour ne pas fausser les calculs).
     */
    public function destroy(Etudiant $etudiant)
    {
        $etudiant->update(['statut' => 'archive']);

        return redirect()->route('etudiants.index')
            ->with('succes', $etudiant->nom_complet . ' a été archivé.');
    }

    /**
     * Restaurer un etudiant archive.
     */
    public function restaurer(Etudiant $etudiant)
    {
        $etudiant->update(['statut' => 'actif']);

        return redirect()->route('etudiants.show', $etudiant)
            ->with('succes', $etudiant->nom_complet . ' a été restauré.');
    }

    /**
     * Applique les filtres communs aux exports.
     */
    private function appliquerFiltresExport(Request $request)
    {
        $query = Etudiant::with('classe');
        $classeNom = null;

        if ($request->filled('classe_id')) {
            if ($request->classe_id === 'sans_classe') {
                $query->whereNull('classe_id');
                $classeNom = 'Sans classe';
            } else {
                $classe = Classe::find((int) $request->classe_id);
                if ($classe) {
                    $query->where('classe_id', $classe->id);
                    $classeNom = $classe->nom;
                }
            }
        }

        if ($request->filled('statut') && in_array($request->statut, ['actif', 'inactif'])) {
            $query->where('statut', $request->statut);
        }

        return [$query, $classeNom];
    }

    public function exportPdf(Request $request)
    {
        [$query, $classeNom] = $this->appliquerFiltresExport($request);

        $etudiants = $query->orderBy('nom')->get();
        $etablissement = Etablissement::first();

        $pdf = Pdf::loadView('exports.etudiants-pdf', compact('etudiants', 'etablissement', 'classeNom'))
            ->setPaper('a4', 'landscape');

        $nomFichier = $classeNom
            ? 'liste_eleves_' . \Str::slug($classeNom) . '.pdf'
            : 'liste_eleves_complete.pdf';

        return $pdf->download($nomFichier);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        [$query, $classeNom] = $this->appliquerFiltresExport($request);

        $nomFichier = $classeNom
            ? 'liste_eleves_' . \Str::slug($classeNom) . '.csv'
            : 'liste_eleves_complete.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 pour compatibilite Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            // En-tete avec "sep=;" pour qu'Excel detecte le separateur
            fprintf($handle, "sep=;\n");
            fputcsv($handle, ['Matricule', 'Prénom', 'Nom', 'Sexe', 'Date Naissance', 'Classe', 'Téléphone', 'Adresse', 'Parent', 'Tél Parent', 'Date Inscription', 'Statut'], ';');

            $query->orderBy('nom')->chunk(200, function ($etudiants) use ($handle) {
                foreach ($etudiants as $e) {
                    fputcsv($handle, [
                        $e->matricule,
                        $e->prenom,
                        $e->nom,
                        ucfirst($e->sexe),
                        $e->date_naissance?->format('d/m/Y') ?? '',
                        $e->classe?->nom ?? '',
                        $e->telephone ?? '',
                        $e->adresse ?? '',
                        $e->nom_parent ?? '',
                        $e->tel_parent ?? '',
                        $e->date_inscription?->format('d/m/Y') ?? '',
                        ucfirst($e->statut),
                    ], ';');
                }
            });

            fclose($handle);
        }, $nomFichier, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nomFichier}\"",
        ]);
    }

    /**
     * Verification de doublon (BUG-022) — appele en AJAX.
     */
    public function verifierDoublon(Request $request)
    {
        $prenom = trim($request->prenom ?? '');
        $nom    = trim($request->nom ?? '');

        if (strlen($prenom) < 2 || strlen($nom) < 2) {
            return response()->json(['doublon' => false]);
        }

        $similaire = Etudiant::where('prenom', 'like', $prenom)
            ->where('nom', 'like', $nom)
            ->first();

        if ($similaire) {
            return response()->json([
                'doublon' => true,
                'message' => "Un etudiant similaire existe deja : {$similaire->nom_complet} (matricule {$similaire->matricule})",
            ]);
        }

        return response()->json(['doublon' => false]);
    }

    /**
     * Genere un matricule unique base sur le dernier numero existant (BUG-001).
     */
    private function genererMatricule(): string
    {
        $annee  = now()->year;
        $prefix = "ETU-{$annee}-";

        $dernier = Etudiant::where('matricule', 'like', "{$prefix}%")
            ->orderByRaw("CAST(SUBSTR(matricule, -4) AS UNSIGNED) DESC")
            ->value('matricule');

        $num = $dernier ? ((int) substr($dernier, -4)) + 1 : 1;

        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Supprime une photo du disque de maniere fiable (BUG-005).
     */
    /**
     * Sert la photo d'un étudiant directement depuis le disque (contourne les symlinks Windows).
     */
    public function servirPhoto(Etudiant $etudiant): BinaryFileResponse|Response
    {
        if (!$etudiant->photo) {
            abort(404);
        }

        $path = storage_path('app/public/etudiants/' . $etudiant->photo);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function supprimerPhoto(?string $nomFichier): void
    {
        if (!$nomFichier) {
            return;
        }
        $chemin = storage_path("app/public/etudiants/{$nomFichier}");
        if (file_exists($chemin)) {
            unlink($chemin);
        }
    }

    private function sauvegarderPhoto($fichier): string
    {
        $nomFichier = 'etudiant_' . bin2hex(random_bytes(12)) . '.jpg';
        $chemin = storage_path("app/public/etudiants/{$nomFichier}");

        if (!is_dir(storage_path('app/public/etudiants'))) {
            mkdir(storage_path('app/public/etudiants'), 0755, true);
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read($fichier->getRealPath())
            ->scale(width: 300)
            ->toJpeg(85);
        file_put_contents($chemin, $image);

        return $nomFichier;
    }

    private function sauvegarderPhotoBase64(string $base64): string
    {
        $imageData = preg_replace('/^data:image\/(jpeg|jpg|png);base64,/', '', $base64);
        $imageData = base64_decode($imageData, true);

        if ($imageData === false || strlen($imageData) > 5 * 1024 * 1024) {
            abort(422, 'Image webcam invalide ou trop volumineuse.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($imageData);
        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
            abort(422, 'Type de fichier non autorisé.');
        }

        $nomFichier = 'etudiant_' . bin2hex(random_bytes(12)) . '.jpg';
        $chemin = storage_path("app/public/etudiants/{$nomFichier}");

        if (!is_dir(storage_path('app/public/etudiants'))) {
            mkdir(storage_path('app/public/etudiants'), 0755, true);
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read($imageData)
            ->scale(width: 300)
            ->toJpeg(85);
        file_put_contents($chemin, $image);

        return $nomFichier;
    }
}
