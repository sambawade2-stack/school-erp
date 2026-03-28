<?php

namespace App\Http\Controllers;

use App\Models\Chambre;
use Illuminate\Http\Request;

class ChambreController extends Controller
{
    public function index()
    {
        $chambres = Chambre::withCount(['internes as actifs_count' => fn($q) => $q->where('statut', 'actif')])
            ->orderBy('numero')
            ->get();

        return view('chambres.index', compact('chambres'));
    }

    public function create()
    {
        return view('chambres.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero'      => 'required|string|max:50|unique:chambres,numero',
            'capacite'    => 'required|integer|min:1|max:50',
            'description' => 'nullable|string|max:255',
        ], [
            'numero.unique' => 'Ce numéro de chambre existe déjà.',
        ]);

        Chambre::create($request->only('numero', 'capacite', 'description'));

        return redirect()->route('chambres.index')
            ->with('succes', 'Chambre créée avec succès.');
    }

    public function edit(Chambre $chambre)
    {
        $chambre->load(['internes' => fn($q) => $q->where('statut', 'actif')->with('etudiant')]);
        return view('chambres.edit', compact('chambre'));
    }

    public function update(Request $request, Chambre $chambre)
    {
        $request->validate([
            'numero'      => 'required|string|max:50|unique:chambres,numero,' . $chambre->id,
            'capacite'    => 'required|integer|min:1|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $chambre->update($request->only('numero', 'capacite', 'description'));

        return redirect()->route('chambres.index')
            ->with('succes', 'Chambre mise à jour.');
    }

    public function destroy(Chambre $chambre)
    {
        if ($chambre->internes()->where('statut', 'actif')->exists()) {
            return back()->withErrors(['chambre' => 'Impossible de supprimer une chambre avec des internes actifs.']);
        }

        $chambre->delete();

        return redirect()->route('chambres.index')
            ->with('succes', 'Chambre supprimée.');
    }
}
