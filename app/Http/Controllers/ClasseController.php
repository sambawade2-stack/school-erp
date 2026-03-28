<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Enseignant;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    public function index()
    {
        $classes = Classe::with('responsable')
            ->withCount(['etudiants' => fn($q) => $q->where('statut', 'actif')])
            ->orderBy('niveau')
            ->orderBy('nom')
            ->paginate(15);

        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $enseignants = Enseignant::where('statut', 'actif')->orderBy('nom')->get();
        $anneeActive = AnneeScolaire::active();
        return view('classes.create', compact('enseignants', 'anneeActive'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'           => 'required|string|max:50',
            'categorie'     => 'required|in:elementaire,college,lycee',
            'niveau'        => 'required|string|max:50',
            'capacite'      => 'required|integer|min:1|max:100',
            'annee_scolaire'=> 'required|string',
            'enseignant_id' => 'nullable|exists:enseignants,id',
            'description'   => 'nullable|string',
        ]);

        Classe::create($data);

        return redirect()->route('classes.index')
            ->with('succes', 'Classe créée avec succès.');
    }

    public function show(Classe $classe)
    {
        $classe->load(['responsable', 'etudiants' => fn($q) => $q->where('statut', 'actif')->orderBy('nom'), 'matieres.enseignant']);
        return view('classes.show', compact('classe'));
    }

    public function edit(Classe $classe)
    {
        $enseignants = Enseignant::where('statut', 'actif')->orderBy('nom')->get();
        return view('classes.edit', compact('classe', 'enseignants'));
    }

    public function update(Request $request, Classe $classe)
    {
        $data = $request->validate([
            'nom'           => 'required|string|max:50',
            'categorie'     => 'required|in:elementaire,college,lycee',
            'niveau'        => 'required|string|max:50',
            'capacite'      => 'required|integer|min:1|max:100',
            'annee_scolaire'=> 'required|string',
            'enseignant_id' => 'nullable|exists:enseignants,id',
            'description'   => 'nullable|string',
        ]);

        $classe->update($data);

        return redirect()->route('classes.show', $classe)
            ->with('succes', 'Classe modifiée avec succès.');
    }

    public function destroy(Classe $classe)
    {
        $classe->delete();
        return redirect()->route('classes.index')
            ->with('succes', 'Classe supprimée.');
    }
}
