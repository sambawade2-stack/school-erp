<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Tarif;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    public function index()
    {
        $anneeActive = AnneeScolaire::active();
        $annees = AnneeScolaire::orderByDesc('date_debut')->pluck('libelle');
        $anneeSelectionnee = request('annee', $anneeActive?->libelle ?? $annees->first());

        $tarifs = Tarif::where('annee_scolaire', $anneeSelectionnee)
            ->orderByRaw("CASE niveau WHEN 'elementaire' THEN 1 WHEN 'college' THEN 2 WHEN 'terminal' THEN 3 ELSE 4 END")
            ->orderBy('type_frais')
            ->get()
            ->groupBy('niveau');

        return view('admin.tarifs.index', compact('tarifs', 'annees', 'anneeSelectionnee', 'anneeActive'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'annee_scolaire' => 'required|string',
            'niveau'         => 'required|in:elementaire,college,terminal',
            'type_frais'     => 'required|string',
            'libelle'        => 'required|string|max:100',
            'montant'        => 'required|numeric|min:0',
        ]);

        Tarif::create($request->only(['annee_scolaire', 'niveau', 'type_frais', 'libelle', 'montant']));

        return back()->with('succes', 'Tarif ajouté.');
    }

    public function update(Request $request, Tarif $tarif)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'libelle' => 'required|string|max:100',
        ]);

        $tarif->update($request->only(['montant', 'libelle']));

        return back()->with('succes', 'Tarif mis à jour.');
    }

    public function destroy(Tarif $tarif)
    {
        $tarif->delete();
        return back()->with('succes', 'Tarif supprimé.');
    }
}
