<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use App\Models\Etablissement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EnseignantController extends Controller
{
    public function index(Request $request)
    {
        $query = Enseignant::query();

        if ($request->filled('recherche')) {
            $s = $request->recherche;
            $query->where(fn($q) => $q->where('nom', 'like', "%{$s}%")
                ->orWhere('prenom', 'like', "%{$s}%")
                ->orWhere('specialite', 'like', "%{$s}%"));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $enseignants = $query->orderBy('nom')->paginate(15)->withQueryString();
        return view('enseignants.index', compact('enseignants'));
    }

    public function create()
    {
        return view('enseignants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'prenom'        => 'required|string|max:100',
            'nom'           => 'required|string|max:100',
            'type'          => 'nullable|string|max:50',
            'email'         => 'nullable|email|unique:enseignants,email',
            'telephone'     => 'nullable|string|max:20',
            'specialite'    => 'nullable|string|max:100',
            'date_embauche' => 'nullable|date',
            'photo'         => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $data = $request->except(['photo', 'photo_webcam']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->sauvegarderPhoto($request->file('photo'));
        } elseif ($request->filled('photo_webcam')) {
            $data['photo'] = $this->sauvegarderPhotoBase64($request->photo_webcam);
        }

        Enseignant::create($data);

        return redirect()->route('enseignants.index')
            ->with('succes', 'Personnel ajouté avec succès.');
    }

    public function show(Enseignant $enseignant)
    {
        $enseignant->load(['matieres.classe', 'emploisDuTemps.classe', 'emploisDuTemps.matiere']);
        return view('enseignants.show', compact('enseignant'));
    }

    public function edit(Enseignant $enseignant)
    {
        return view('enseignants.edit', compact('enseignant'));
    }

    public function update(Request $request, Enseignant $enseignant)
    {
        $request->validate([
            'prenom'        => 'required|string|max:100',
            'nom'           => 'required|string|max:100',
            'type'          => 'nullable|string|max:50',
            'email'         => 'nullable|email|unique:enseignants,email,' . $enseignant->id,
            'telephone'     => 'nullable|string|max:20',
            'specialite'    => 'nullable|string|max:100',
            'date_embauche' => 'nullable|date',
            'photo'         => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $data = $request->except(['photo', 'photo_webcam']);

        if ($request->hasFile('photo')) {
            if ($enseignant->photo) {
                Storage::delete("public/enseignants/{$enseignant->photo}");
            }
            $data['photo'] = $this->sauvegarderPhoto($request->file('photo'));
        } elseif ($request->filled('photo_webcam')) {
            if ($enseignant->photo) {
                Storage::delete("public/enseignants/{$enseignant->photo}");
            }
            $data['photo'] = $this->sauvegarderPhotoBase64($request->photo_webcam);
        }

        $enseignant->update($data);

        return redirect()->route('enseignants.show', $enseignant)
            ->with('succes', 'Personnel modifié avec succès.');
    }

    public function destroy(Enseignant $enseignant)
    {
        if ($enseignant->photo) {
            Storage::delete("public/enseignants/{$enseignant->photo}");
        }
        $enseignant->delete();

        return redirect()->route('enseignants.index')
            ->with('succes', 'Personnel supprimé.');
    }

    public function exportPdf(Request $request)
    {
        $query = Enseignant::query();
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $personnels = $query->orderBy('nom')->get();
        $etablissement = Etablissement::first();

        $pdf = Pdf::loadView('exports.personnel-pdf', compact('personnels', 'etablissement'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('liste_personnel.pdf');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Enseignant::query();
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $personnels = $query->orderBy('nom')->get();

        return response()->streamDownload(function () use ($personnels) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Prénom', 'Nom', 'Type', 'Spécialité', 'Email', 'Téléphone', 'Date embauche', 'Statut'], ';');
            foreach ($personnels as $p) {
                fputcsv($handle, [
                    $p->prenom, $p->nom, Enseignant::TYPES[$p->type] ?? $p->type,
                    $p->specialite ?? '', $p->email ?? '', $p->telephone ?? '',
                    $p->date_embauche?->format('d/m/Y') ?? '', ucfirst($p->statut),
                ], ';');
            }
            fclose($handle);
        }, 'liste_personnel.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function servirPhoto(Enseignant $enseignant): BinaryFileResponse|Response
    {
        if (!$enseignant->photo) {
            abort(404);
        }

        $path = storage_path('app/public/enseignants/' . $enseignant->photo);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function sauvegarderPhoto($fichier): string
    {
        $nomFichier = 'enseignant_' . bin2hex(random_bytes(8)) . '.jpg';
        $chemin = storage_path("app/public/enseignants/{$nomFichier}");

        if (!is_dir(storage_path('app/public/enseignants'))) {
            mkdir(storage_path('app/public/enseignants'), 0755, true);
        }

        $image = Image::read($fichier->getRealPath())
            ->scale(width: 300)
            ->toJpeg(85);
        file_put_contents($chemin, $image);

        return $nomFichier;
    }

    private function sauvegarderPhotoBase64(string $base64): string
    {
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $imageData = base64_decode($imageData);

        $nomFichier = 'enseignant_' . bin2hex(random_bytes(8)) . '.jpg';
        $chemin = storage_path("app/public/enseignants/{$nomFichier}");

        if (!is_dir(storage_path('app/public/enseignants'))) {
            mkdir(storage_path('app/public/enseignants'), 0755, true);
        }

        $image = Image::read($imageData)
            ->scale(width: 300)
            ->toJpeg(85);
        file_put_contents($chemin, $image);

        return $nomFichier;
    }
}
