<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Matiere;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    public function index()
    {
        $matieres = Matiere::with(['enseignant', 'classe'])->orderBy('nom')->paginate(15);
        return view('matieres.index', compact('matieres'));
    }

    public function create()
    {
        $enseignants = Enseignant::where('statut', 'actif')->orderBy('nom')->get();
        $classes     = Classe::orderBy('nom')->get();
        return view('matieres.create', compact('enseignants', 'classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'          => 'required|string|max:100',
            'code'         => 'nullable|string|max:20',
            'coefficient'  => 'required|numeric|min:0.5',
            'niveau'       => 'nullable|string|max:50',
            'section'      => 'nullable|string|max:100',
            'enseignant_id'=> 'nullable|exists:enseignants,id',
            'classe_id'    => 'nullable|exists:classes,id',
        ]);

        Matiere::create($data);

        return redirect()->route('matieres.index')->with('succes', 'Matière créée avec succès.');
    }

    public function edit(Matiere $matiere)
    {
        $enseignants = Enseignant::where('statut', 'actif')->orderBy('nom')->get();
        $classes     = Classe::orderBy('nom')->get();
        return view('matieres.edit', compact('matiere', 'enseignants', 'classes'));
    }

    public function update(Request $request, Matiere $matiere)
    {
        $data = $request->validate([
            'nom'          => 'required|string|max:100',
            'code'         => 'nullable|string|max:20',
            'coefficient'  => 'required|numeric|min:0.5',
            'niveau'       => 'nullable|string|max:50',
            'section'      => 'nullable|string|max:100',
            'enseignant_id'=> 'nullable|exists:enseignants,id',
            'classe_id'    => 'nullable|exists:classes,id',
        ]);

        $matiere->update($data);

        return redirect()->route('matieres.index')->with('succes', 'Matière modifiée.');
    }

    public function destroy(Matiere $matiere)
    {
        $matiere->delete();
        return redirect()->route('matieres.index')->with('succes', 'Matière supprimée.');
    }
}
