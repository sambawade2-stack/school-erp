<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Etudiant;
use App\Models\Presence;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $classes  = Classe::orderBy('nom')->get();
        $classeId = $request->filled('classe_id') ? (int) $request->classe_id : $classes->first()?->id;
        $date     = ($request->filled('date') && \DateTime::createFromFormat('Y-m-d', $request->date))
            ? $request->date
            : today()->format('Y-m-d');

        $etudiants = Etudiant::where('classe_id', $classeId)
            ->where('statut', 'actif')
            ->orderBy('nom')
            ->get();

        $presences = Presence::where('classe_id', $classeId)
            ->whereDate('date', $date)
            ->pluck('statut', 'etudiant_id');

        return view('presences.index', compact('classes', 'etudiants', 'presences', 'classeId', 'date'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'classe_id'  => 'required|exists:classes,id',
            'date'       => 'required|date',
            'presences'  => 'array',
        ]);

        $classeId = $request->classe_id;
        $date = $request->date;
        $etudiants = Etudiant::where('classe_id', $classeId)->where('statut', 'actif')->get();

        foreach ($etudiants as $etudiant) {
            $statut = $request->presences[$etudiant->id] ?? 'absent';
            Presence::updateOrCreate(
                ['etudiant_id' => $etudiant->id, 'date' => $date],
                ['classe_id' => $classeId, 'statut' => $statut]
            );
        }

        return redirect()->route('presences.index', ['classe_id' => $classeId, 'date' => $date])
            ->with('succes', 'Présences enregistrées pour le ' . \Carbon\Carbon::parse($date)->format('d/m/Y'));
    }

    public function rapport(Request $request)
    {
        $classes = Classe::orderBy('nom')->get();
        $classeId = $request->classe_id;
        $debut = $request->debut ?? now()->startOfMonth()->format('Y-m-d');
        $fin = $request->fin ?? now()->format('Y-m-d');

        $rapport = null;
        if ($classeId) {
            $rapport = Etudiant::where('classe_id', $classeId)
                ->where('statut', 'actif')
                ->with(['presences' => fn($q) => $q->whereBetween('date', [$debut, $fin])])
                ->orderBy('nom')
                ->get()
                ->map(function ($etudiant) {
                    return [
                        'etudiant'  => $etudiant,
                        'present'   => $etudiant->presences->where('statut', 'present')->count(),
                        'absent'    => $etudiant->presences->where('statut', 'absent')->count(),
                        'retard'    => $etudiant->presences->where('statut', 'retard')->count(),
                        'excuse'    => $etudiant->presences->where('statut', 'excuse')->count(),
                        'total'     => $etudiant->presences->count(),
                    ];
                });
        }

        return view('presences.rapport', compact('classes', 'classeId', 'debut', 'fin', 'rapport'));
    }
}
