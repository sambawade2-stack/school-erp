<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Depense;
use App\Models\Enseignant;
use App\Models\Etablissement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DepenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Depense::query();

        if ($request->filled('recherche')) {
            $s = $request->recherche;
            $query->where('libelle', 'like', "%{$s}%")
                  ->orWhere('beneficiaire', 'like', "%{$s}%");
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        $moisFiltre = $request->filled('mois') ? (int) $request->mois : now()->month;
        $anneeFiltre = $request->filled('annee') ? (int) $request->annee : now()->year;

        if ($request->filled('mois')) {
            $query->whereMonth('date_depense', $moisFiltre)
                  ->whereYear('date_depense', $anneeFiltre);
        }

        $depenses = $query->orderByDesc('date_depense')->paginate(20)->withQueryString();

        $statsQuery = Depense::whereMonth('date_depense', $moisFiltre)
            ->whereYear('date_depense', $anneeFiltre);

        $totalMois = (clone $statsQuery)->sum('montant');
        $totalAnnee = Depense::whereYear('date_depense', $anneeFiltre)->sum('montant');

        $totauxParCategorie = (clone $statsQuery)
            ->selectRaw("categorie, SUM(montant) as total")
            ->groupBy('categorie')
            ->pluck('total', 'categorie');

        $categories = ['fournitures', 'salaires', 'maintenance', 'transport', 'alimentation', 'autre'];

        return view('depenses.index', compact(
            'depenses', 'totalMois', 'totalAnnee', 'totauxParCategorie',
            'categories', 'moisFiltre', 'anneeFiltre'
        ));
    }

    public function create(Request $request)
    {
        $anneeActive = AnneeScolaire::libelleActif() ?? date('Y') . '-' . (date('Y') + 1);
        $categorie = $request->categorie;
        $personnels = Enseignant::where('statut', 'actif')->orderBy('nom')->get();
        return view('depenses.create', compact('anneeActive', 'categorie', 'personnels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'libelle'        => 'required|string|max:255',
            'montant'        => 'required|numeric|min:0.01',
            'categorie'      => 'required|in:fournitures,salaires,maintenance,transport,alimentation,autre',
            'date_depense'   => 'required|date',
            'annee_scolaire' => 'required|string|max:20',
            'beneficiaire'   => 'nullable|string|max:255',
            'description'    => 'nullable|string|max:1000',
        ]);

        Depense::create($data);

        return redirect()->route('depenses.index')
            ->with('succes', 'Dépense enregistrée avec succès.');
    }

    public function edit(Depense $depense)
    {
        return view('depenses.edit', compact('depense'));
    }

    public function update(Request $request, Depense $depense)
    {
        $data = $request->validate([
            'libelle'        => 'required|string|max:255',
            'montant'        => 'required|numeric|min:0.01',
            'categorie'      => 'required|in:fournitures,salaires,maintenance,transport,alimentation,autre',
            'date_depense'   => 'required|date',
            'annee_scolaire' => 'required|string|max:20',
            'beneficiaire'   => 'nullable|string|max:255',
            'description'    => 'nullable|string|max:1000',
        ]);

        $depense->update($data);

        return redirect()->route('depenses.index')
            ->with('succes', 'Dépense mise à jour.');
    }

    public function destroy(Depense $depense)
    {
        $depense->delete();
        return redirect()->route('depenses.index')
            ->with('succes', 'Dépense supprimée.');
    }

    public function exportPdf(Request $request)
    {
        $query = Depense::query();
        $mois = $request->filled('mois') ? (int) $request->mois : now()->month;
        $annee = $request->filled('annee') ? (int) $request->annee : now()->year;

        $query->whereMonth('date_depense', $mois)->whereYear('date_depense', $annee);

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        $depenses = $query->orderByDesc('date_depense')->get();
        $total = $depenses->sum('montant');
        $etablissement = Etablissement::first();
        $periode = \Carbon\Carbon::create($annee, $mois, 1)->locale('fr')->translatedFormat('F Y');

        $pdf = Pdf::loadView('exports.depenses-pdf', compact('depenses', 'total', 'etablissement', 'periode'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('depenses_' . $mois . '_' . $annee . '.pdf');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Depense::query();
        $mois = $request->filled('mois') ? (int) $request->mois : now()->month;
        $annee = $request->filled('annee') ? (int) $request->annee : now()->year;

        $query->whereMonth('date_depense', $mois)->whereYear('date_depense', $annee);

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        $depenses = $query->orderByDesc('date_depense')->get();

        return response()->streamDownload(function () use ($depenses) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, ['Libellé', 'Montant', 'Catégorie', 'Date', 'Bénéficiaire', 'Description'], ';');
            foreach ($depenses as $d) {
                fputcsv($handle, [
                    $d->libelle, $d->montant, $d->categorie,
                    $d->date_depense->format('d/m/Y'), $d->beneficiaire ?? '', $d->description ?? '',
                ], ';');
            }
            fclose($handle);
        }, 'depenses_' . $mois . '_' . $annee . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
