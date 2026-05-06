<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Composition;
use App\Models\Matiere;
use Illuminate\Http\Request;

class CompositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Composition::with(['matiere', 'classe']);

        if ($request->filled('classe_id')) {
            $query->where('classe_id', $request->classe_id);
        }

        $compositions = $query->orderByDesc('date_composition')->paginate(15)->withQueryString();
        $classes = Classe::orderBy('nom')->get();

        return view('compositions.index', compact('compositions', 'classes'));
    }

    public function create()
    {
        $classes  = Classe::orderBy('nom')->get();
        $matieres = Matiere::with('classes')->orderBy('nom')->get();
        return view('compositions.create', compact('classes', 'matieres'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'intitule'         => 'required|string|max:150',
            'matiere_id'       => 'required|exists:matieres,id',
            'classe_ids'       => 'required|array|min:1',
            'classe_ids.*'     => 'exists:classes,id',
            'date_composition' => 'required|date',
            'note_max'         => 'required|numeric|min:1',
            'annee_scolaire'   => 'required|string',
            'trimestre'        => 'required|in:T1,T2,T3,S1,S2,S3',
        ]);

        $classeIds = $data['classe_ids'];
        unset($data['classe_ids']);

        foreach ($classeIds as $classeId) {
            Composition::create(array_merge($data, ['classe_id' => $classeId]));
        }

        $nb = count($classeIds);
        $msg = $nb > 1 ? "{$nb} compositions créées avec succès." : 'Composition créée avec succès.';

        return redirect()->route('examens.index', ['tab' => 'compositions'])->with('succes', $msg);
    }

    public function show(Composition $composition)
    {
        $composition->load(['matiere', 'classe', 'notes.etudiant']);
        $etudiants = $composition->classe->etudiants()->where('statut', 'actif')->orderBy('nom')->get();

        return view('compositions.show', compact('composition', 'etudiants'));
    }

    public function edit(Composition $composition)
    {
        $classes  = Classe::orderBy('nom')->get();
        $matieres = Matiere::with('classes')->orderBy('nom')->get();
        return view('compositions.edit', compact('composition', 'classes', 'matieres'));
    }

    public function update(Request $request, Composition $composition)
    {
        $data = $request->validate([
            'intitule'         => 'required|string|max:150',
            'matiere_id'       => 'required|exists:matieres,id',
            'classe_ids'       => 'required|array|min:1',
            'classe_ids.*'     => 'exists:classes,id',
            'date_composition' => 'required|date',
            'note_max'         => 'required|numeric|min:1',
            'annee_scolaire'   => 'required|string',
            'trimestre'        => 'required|in:T1,T2,T3,S1,S2,S3',
        ]);

        $classeIds = $data['classe_ids'];
        unset($data['classe_ids']);

        $composition->update($data);

        // Créer de nouvelles compositions pour les classes supplémentaires
        $nouvelles = 0;
        foreach ($classeIds as $classeId) {
            if ($classeId != $composition->classe_id) {
                Composition::create(array_merge($data, ['classe_id' => $classeId]));
                $nouvelles++;
            }
        }

        $msg = $nouvelles > 0
            ? "Composition mise à jour + {$nouvelles} nouvelle(s) créée(s)."
            : 'Composition mise à jour.';

        return redirect()->route('examens.index', ['tab' => 'compositions'])->with('succes', $msg);
    }

    public function destroy(Composition $composition)
    {
        $composition->delete();
        return redirect()->route('examens.index', ['tab' => 'compositions'])->with('succes', 'Composition supprimée.');
    }
}
