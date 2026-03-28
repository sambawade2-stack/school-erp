<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\TranchePaiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TranchePaiementController extends Controller
{
    public function store(Request $request, Paiement $paiement)
    {
        $request->validate([
            'montant'       => 'required|numeric|min:0.01',
            'date_paiement' => 'required|date',
            'remarque'      => 'nullable|string',
        ]);

        $montantTotal = $paiement->montant_total ?? $paiement->montant;
        $dejaPayé     = $paiement->montant;
        $restant      = $montantTotal - $dejaPayé;

        if ($request->montant > $restant + 0.01) {
            return back()->withErrors(['montant' => "Le montant dépasse le restant dû ({$restant} XOF)."]);
        }

        $tranche = DB::transaction(function () use ($paiement, $request, $dejaPayé, $montantTotal) {
            $tranche = TranchePaiement::create([
                'paiement_id'   => $paiement->id,
                'montant'       => $request->montant,
                'date_paiement' => $request->date_paiement,
                'numero_recu'   => $this->genererNumero(),
                'remarque'      => $request->remarque,
            ]);

            $nouveauMontant = $dejaPayé + $request->montant;
            $statut = $nouveauMontant >= $montantTotal ? 'complet' : 'partiel';
            $paiement->update(['montant' => $nouveauMontant, 'statut' => $statut]);

            return $tranche;
        });

        return redirect()->route('paiements.show', $paiement)
            ->with('succes', "Tranche enregistrée. Reçu N° {$tranche->numero_recu}");
    }

    public function destroy(TranchePaiement $tranche)
    {
        $paiement = $tranche->paiement;

        DB::transaction(function () use ($paiement, $tranche) {
            $montantTotal = $paiement->montant_total ?? $paiement->montant;

            $paiement->decrement('montant', $tranche->montant);
            $paiement->refresh();

            $statut = $paiement->montant >= $montantTotal ? 'complet'
                : ($paiement->montant > 0 ? 'partiel' : 'non_paye');
            $paiement->update(['statut' => $statut]);

            $tranche->delete();
        });

        return redirect()->route('paiements.show', $paiement)
            ->with('succes', 'Tranche supprimée.');
    }

    private function genererNumero(): string
    {
        $dernier = TranchePaiement::max('id') ?? 0;
        return 'TR-' . now()->year . '-' . str_pad($dernier + 1, 5, '0', STR_PAD_LEFT);
    }
}
