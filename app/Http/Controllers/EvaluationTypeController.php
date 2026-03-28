<?php

namespace App\Http\Controllers;

use App\Models\EvaluationType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EvaluationTypeController extends Controller
{
    public function index()
    {
        $types      = EvaluationType::orderBy('poids', 'desc')->get();
        $sommePoids = EvaluationType::sommePoids();

        return view('evaluation_types.index', compact('types', 'sommePoids'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:100',
            'poids'       => 'required|numeric|min:0.01|max:1',
            'couleur'     => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
        ]);

        $data['slug']    = Str::slug($data['nom']);
        $data['couleur'] = $data['couleur'] ?? '#3B82F6';

        EvaluationType::create($data);

        return back()->with('succes', 'Type d\'évaluation créé avec succès.');
    }

    public function update(Request $request, EvaluationType $evaluationType)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:100',
            'poids'       => 'required|numeric|min:0.01|max:1',
            'couleur'     => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
        ]);

        $data['slug'] = Str::slug($data['nom']);

        $evaluationType->update($data);

        return back()->with('succes', 'Type d\'évaluation mis à jour.');
    }

    public function destroy(EvaluationType $evaluationType)
    {
        if ($evaluationType->notes()->count() > 0) {
            return back()->with('erreur', 'Impossible de supprimer : des notes utilisent ce type.');
        }

        $evaluationType->delete();

        return back()->with('succes', 'Type d\'évaluation supprimé.');
    }
}
