<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::ordonnes()->withCount('matieres')->get();
        return view('sections.index', compact('sections'));
    }

    public function create()
    {
        return view('sections.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'     => 'required|string|max:100|unique:sections,nom',
            'couleur' => 'required|string|max:7',
            'niveau'  => 'nullable|string|in:elementaire,college,lycee',
            'ordre'   => 'nullable|integer|min:0',
        ]);

        $data['ordre'] = $data['ordre'] ?? 0;

        Section::create($data);

        return redirect()->route('sections.index')->with('success', 'Section créée avec succès.');
    }

    public function edit(Section $section)
    {
        return view('sections.edit', compact('section'));
    }

    public function update(Request $request, Section $section)
    {
        $data = $request->validate([
            'nom'     => 'required|string|max:100|unique:sections,nom,' . $section->id,
            'couleur' => 'required|string|max:7',
            'niveau'  => 'nullable|string|in:elementaire,college,lycee',
            'ordre'   => 'nullable|integer|min:0',
        ]);

        $data['ordre'] = $data['ordre'] ?? 0;

        // Mettre à jour le nom dans les matières si changé
        if ($section->nom !== $data['nom']) {
            $section->matieres()->update(['section' => $data['nom']]);
        }

        $section->update($data);

        return redirect()->route('sections.index')->with('success', 'Section mise à jour.');
    }

    public function destroy(Section $section)
    {
        // Remettre les matières en "Générale"
        $section->matieres()->update(['section' => 'Générale']);
        $section->delete();

        return redirect()->route('sections.index')->with('success', 'Section supprimée.');
    }
}
