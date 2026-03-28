<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Etudiant;
use App\Models\Paiement;
use App\Models\Presence;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RapportController extends Controller
{
    public function index()
    {
        return view('rapports.index');
    }

    public function etudiants(Request $request)
    {
        $query = Etudiant::with('classe');

        if ($request->filled('classe_id')) {
            $query->where('classe_id', $request->classe_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $etudiants = $query->orderBy('nom')->get();
        $classes   = Classe::orderBy('nom')->get();

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('rapports.pdf.etudiants', compact('etudiants'));
            return $pdf->download('liste-etudiants.pdf');
        }

        return view('rapports.etudiants', compact('etudiants', 'classes'));
    }

    public function paiements(Request $request)
    {
        $debut = $request->debut ?? now()->startOfMonth()->format('Y-m-d');
        $fin   = $request->fin   ?? now()->format('Y-m-d');

        $paiements = Paiement::with('etudiant')
            ->whereBetween('date_paiement', [$debut, $fin])
            ->orderByDesc('date_paiement')
            ->get();

        $total = $paiements->sum('montant');

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('rapports.pdf.paiements', compact('paiements', 'total', 'debut', 'fin'));
            return $pdf->download('rapport-paiements.pdf');
        }

        return view('rapports.paiements', compact('paiements', 'total', 'debut', 'fin'));
    }

    public function presences(Request $request)
    {
        $classes = Classe::orderBy('nom')->get();
        $classeId = $request->classe_id;
        $debut = $request->debut ?? now()->startOfMonth()->format('Y-m-d');
        $fin   = $request->fin   ?? now()->format('Y-m-d');

        $rapport = null;
        if ($classeId) {
            $rapport = Etudiant::where('classe_id', $classeId)
                ->where('statut', 'actif')
                ->with(['presences' => fn($q) => $q->whereBetween('date', [$debut, $fin])])
                ->orderBy('nom')
                ->get();
        }

        if ($request->format === 'pdf' && $rapport) {
            $pdf = Pdf::loadView('rapports.pdf.presences', compact('rapport', 'debut', 'fin'));
            return $pdf->download('rapport-presences.pdf');
        }

        return view('rapports.presences', compact('classes', 'classeId', 'debut', 'fin', 'rapport'));
    }

    public function bulletins(): View
    {
        $classes = Classe::orderBy('nom')->get();
        return view('rapports.bulletins-group', compact('classes'));
    }
}
