<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Composition;
use App\Models\Devoir;
use App\Models\Examen;
use App\Models\Matiere;
use Illuminate\Http\Request;

class ExamenController extends Controller
{
    public function index(Request $request)
    {
        $classeId  = $request->classe_id;
        $trimestre = $request->trimestre;

        $anneeActive    = AnneeScolaire::active();
        $trimestreActuel = $anneeActive?->trimestre_actuel ?? 'T1';

        $qExamens = Examen::with(['matiere', 'classe']);
        $qDevoirs = Devoir::with(['matiere', 'classe']);
        $qCompos  = Composition::with(['matiere', 'classe']);

        if ($classeId) {
            $qExamens->where('classe_id', $classeId);
            $qDevoirs->where('classe_id', $classeId);
            $qCompos->where('classe_id', $classeId);
        }

        if ($trimestre) {
            $qExamens->where('trimestre', $trimestre);
            $qDevoirs->where('trimestre', $trimestre);
            $qCompos->where('trimestre', $trimestre);
        }

        $examens      = $qExamens->orderByDesc('date_examen')->get();
        $devoirs       = $qDevoirs->orderByDesc('date_devoir')->get();
        $compositions  = $qCompos->orderByDesc('date_composition')->get();
        $classes       = Classe::orderBy('nom')->get();

        return view('examens.index', compact('examens', 'devoirs', 'compositions', 'classes', 'trimestreActuel'));
    }

    public function create()
    {
        $classes  = Classe::orderBy('nom')->get();
        $matieres = Matiere::with('classe')->orderBy('nom')->get();
        return view('examens.create', compact('classes', 'matieres'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'intitule'      => 'required|string|max:150',
            'matiere_id'    => 'required|exists:matieres,id',
            'classe_id'     => 'required|exists:classes,id',
            'date_examen'   => 'required|date',
            'note_max'      => 'required|numeric|min:1',
            'annee_scolaire'=> 'required|string|max:20',
            'trimestre'     => 'required|in:T1,T2,T3,S1,S2,S3',
        ]);

        Examen::create($data);

        return redirect()->route('examens.index', ['tab' => 'examens'])
            ->with('succes', 'Examen créé avec succès.');
    }

    public function show(Examen $examen)
    {
        $examen->load(['matiere', 'classe', 'notes.etudiant']);
        $etudiants = $examen->classe->etudiants()->where('statut', 'actif')->orderBy('nom')->get();

        return view('examens.show', compact('examen', 'etudiants'));
    }

    public function edit(Examen $examen)
    {
        $classes  = Classe::orderBy('nom')->get();
        $matieres = Matiere::with('classe')->orderBy('nom')->get();
        return view('examens.edit', compact('examen', 'classes', 'matieres'));
    }

    public function update(Request $request, Examen $examen)
    {
        $data = $request->validate([
            'intitule'      => 'required|string|max:150',
            'matiere_id'    => 'required|exists:matieres,id',
            'classe_id'     => 'required|exists:classes,id',
            'date_examen'   => 'required|date',
            'note_max'      => 'required|numeric|min:1',
            'annee_scolaire'=> 'required|string|max:20',
            'trimestre'     => 'required|in:T1,T2,T3,S1,S2,S3',
        ]);

        $examen->update($data);

        return redirect()->route('examens.show', $examen)
            ->with('succes', 'Examen modifié avec succès.');
    }

    public function destroy(Examen $examen)
    {
        $examen->delete();
        return redirect()->route('examens.index', ['tab' => 'examens'])->with('succes', 'Examen supprimé.');
    }
}
