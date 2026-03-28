<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Chambre;
use App\Models\Etudiant;
use App\Models\Interne;
use Illuminate\Http\Request;

class InterneController extends Controller
{
    public function index(Request $request)
    {
        $query = Interne::with('etudiant.classe', 'chambreObj');

        if ($request->filled('recherche')) {
            $s = $request->recherche;
            $query->whereHas('etudiant', fn($q) =>
                $q->where('nom', 'like', "%{$s}%")->orWhere('prenom', 'like', "%{$s}%")
            );
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('chambre_id')) {
            $query->where('chambre_id', $request->chambre_id);
        }

        $internes    = $query->latest()->paginate(20)->withQueryString();
        $totalActifs = Interne::where('statut', 'actif')->count();
        $chambres    = Chambre::withCount(['internes as actifs_count' => fn($q) => $q->where('statut', 'actif')])
                              ->orderBy('numero')->get();

        return view('internes.index', compact('internes', 'totalActifs', 'chambres'));
    }

    public function create()
    {
        $etudiants = Etudiant::where('statut', 'actif')
            ->whereDoesntHave('internes', fn($q) => $q->where('statut', 'actif'))
            ->orderBy('nom')->get();

        $anneeActive = AnneeScolaire::libelleActif() ?? date('Y') . '-' . (date('Y') + 1);

        $chambres = Chambre::withCount(['internes as actifs_count' => fn($q) => $q->where('statut', 'actif')])
                           ->orderBy('numero')->get()
                           ->each(fn($c) => $c->pleine = $c->actifs_count >= $c->capacite);

        return view('internes.create', compact('etudiants', 'anneeActive', 'chambres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id'    => 'required|exists:etudiants,id',
            'chambre_id'     => 'nullable|exists:chambres,id',
            'date_entree'    => 'required|date',
            'annee_scolaire' => 'required|string',
            'remarque'       => 'nullable|string',
        ]);

        if ($request->filled('chambre_id')) {
            $chambre = Chambre::withCount(['internes as actifs_count' => fn($q) => $q->where('statut', 'actif')])->findOrFail($request->chambre_id);
            if ($chambre->actifs_count >= $chambre->capacite) {
                return back()->withErrors(['chambre_id' => 'Cette chambre est pleine.'])->withInput();
            }
        }

        Interne::create($request->only('etudiant_id', 'chambre_id', 'date_entree', 'annee_scolaire', 'remarque'));

        return redirect()->route('internes.index')
            ->with('succes', 'Interne enregistré avec succès.');
    }

    public function edit(Interne $interne)
    {
        $interne->load('etudiant', 'chambreObj');

        $chambres = Chambre::withCount(['internes as actifs_count' => fn($q) => $q->where('statut', 'actif')])
                           ->orderBy('numero')->get()
                           ->each(fn($c) => $c->pleine = $c->actifs_count >= $c->capacite);

        $etudiants = Etudiant::where('statut', 'actif')->orderBy('nom')->get();

        return view('internes.edit', compact('interne', 'etudiants', 'chambres'));
    }

    public function update(Request $request, Interne $interne)
    {
        $request->validate([
            'chambre_id'     => 'nullable|exists:chambres,id',
            'date_entree'    => 'required|date',
            'date_sortie'    => 'nullable|date|after_or_equal:date_entree',
            'statut'         => 'required|in:actif,sorti',
            'annee_scolaire' => 'required|string',
            'remarque'       => 'nullable|string',
        ]);

        if ($request->filled('chambre_id') && $request->chambre_id != $interne->chambre_id) {
            $chambre = Chambre::withCount(['internes as actifs_count' => fn($q) => $q->where('statut', 'actif')])->findOrFail($request->chambre_id);
            if ($chambre->actifs_count >= $chambre->capacite) {
                return back()->withErrors(['chambre_id' => 'Cette chambre est pleine.'])->withInput();
            }
        }

        $interne->update($request->only('chambre_id', 'date_entree', 'date_sortie', 'statut', 'annee_scolaire', 'remarque'));

        return redirect()->route('internes.index')
            ->with('succes', 'Interne mis à jour.');
    }

    public function destroy(Interne $interne)
    {
        $interne->delete();
        return redirect()->route('internes.index')->with('succes', 'Interne supprimé.');
    }
}
