<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Devoir;
use App\Models\Matiere;
use Illuminate\Http\Request;

class DevoirController extends Controller
{
    public function index(Request $request)
    {
        $query = Devoir::with(['matiere', 'classe']);

        if ($request->filled('classe_id')) {
            $query->where('classe_id', $request->classe_id);
        }

        $devoirs = $query->orderByDesc('date_devoir')->paginate(15)->withQueryString();
        $classes = Classe::orderBy('nom')->get();

        return view('devoirs.index', compact('devoirs', 'classes'));
    }

    public function create()
    {
        $classes  = Classe::orderBy('nom')->get();
        $matieres = Matiere::with('classe')->orderBy('nom')->get();
        return view('devoirs.create', compact('classes', 'matieres'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'intitule'      => 'required|string|max:150',
            'matiere_id'    => 'required|exists:matieres,id',
            'classe_id'     => 'required|exists:classes,id',
            'date_devoir'   => 'required|date',
            'note_max'      => 'required|numeric|min:1',
            'annee_scolaire'=> 'required|string',
            'trimestre'     => 'required|in:T1,T2,T3,S1,S2,S3',
        ]);

        Devoir::create($data);

        return redirect()->route('examens.index', ['tab' => 'devoirs'])
            ->with('succes', 'Devoir créé avec succès.');
    }

    public function show(Devoir $devoir)
    {
        $devoir->load(['matiere', 'classe', 'notes.etudiant']);
        $etudiants = $devoir->classe->etudiants()->where('statut', 'actif')->orderBy('nom')->get();

        return view('devoirs.show', compact('devoir', 'etudiants'));
    }

    public function edit(Devoir $devoir)
    {
        $classes  = Classe::orderBy('nom')->get();
        $matieres = Matiere::with('classe')->orderBy('nom')->get();
        return view('devoirs.edit', compact('devoir', 'classes', 'matieres'));
    }

    public function update(Request $request, Devoir $devoir)
    {
        $data = $request->validate([
            'intitule'      => 'required|string|max:150',
            'matiere_id'    => 'required|exists:matieres,id',
            'classe_id'     => 'required|exists:classes,id',
            'date_devoir'   => 'required|date',
            'note_max'      => 'required|numeric|min:1',
            'annee_scolaire'=> 'required|string',
            'trimestre'     => 'required|in:T1,T2,T3,S1,S2,S3',
        ]);

        $devoir->update($data);

        return redirect()->route('examens.index', ['tab' => 'devoirs'])
            ->with('succes', 'Devoir modifié avec succès.');
    }

    public function destroy(Devoir $devoir)
    {
        $devoir->delete();
        return redirect()->route('examens.index', ['tab' => 'devoirs'])->with('succes', 'Devoir supprimé.');
    }
}
