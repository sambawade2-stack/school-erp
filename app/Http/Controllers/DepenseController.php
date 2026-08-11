<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Depense;
use App\Models\Enseignant;
use App\Models\Etablissement;
use App\Traits\FiltrePeriodeScolaire;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DepenseController extends Controller
{
    use FiltrePeriodeScolaire;

    public function index(Request $request)
    {
        $query = Depense::query();

        if ($request->filled('recherche')) {
            $s = $request->recherche;
            $query->where(function ($q) use ($s) {
                $q->where('libelle', 'like', "%{$s}%")
                  ->orWhere('beneficiaire', 'like', "%{$s}%");
            });
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('type_mouvement')) {
            $query->where('type_mouvement', $request->type_mouvement);
        }

        [$moisFiltre, $anneeFiltre, $toutMois] = $this->periodeDemandee($request);

        // Bornes de l'année scolaire (à cheval sur deux années civiles) contenant le mois affiché.
        $moisReference           = Carbon::create($anneeFiltre, $moisFiltre, 1);
        [$debutAnnee, $finAnnee] = AnneeScolaire::bornesPourDate($moisReference);
        $libelleAnneeScolaire    = AnneeScolaire::libellePourDate($moisReference);

        $this->filtrerSurPeriode($query, 'date_depense', $moisFiltre, $anneeFiltre, $toutMois);

        $depenses = $query->orderByDesc('date_depense')->paginate(20)->withQueryString();

        $statsQuery = $this->filtrerSurPeriode(Depense::query(), 'date_depense', $moisFiltre, $anneeFiltre, $toutMois);

        $totalDepensesMois  = (clone $statsQuery)->where('type_mouvement', 'depense')->sum('montant');
        $totalDepotsMois    = (clone $statsQuery)->where('type_mouvement', 'depot_banque')->sum('montant');
        $totalRetraitsMois  = (clone $statsQuery)->where('type_mouvement', 'retrait_banque')->sum('montant');

        // Total annuel calculé sur l'année scolaire, et non sur l'année civile :
        // les dépenses de décembre restent comptabilisées après le 1er janvier.
        $totalAnnee = Depense::whereBetween('date_depense', [$debutAnnee, $finAnnee])
            ->where('type_mouvement', 'depense')
            ->sum('montant');

        // Périodes sélectionnables, groupées par année scolaire (décembre inclus).
        $periodeSelectionne = $toutMois ? $libelleAnneeScolaire : sprintf('%04d-%02d', $anneeFiltre, $moisFiltre);
        $moisParAnnee       = $this->optionsPeriodeScolaire($debutAnnee, $finAnnee, $libelleAnneeScolaire, $periodeSelectionne);

        $totauxParCategorie = (clone $statsQuery)
            ->where('type_mouvement', 'depense')
            ->selectRaw("categorie, SUM(montant) as total")
            ->groupBy('categorie')
            ->pluck('total', 'categorie');

        $categories = ['fournitures', 'salaires', 'maintenance', 'transport', 'alimentation', 'autre'];

        return view('depenses.index', compact(
            'depenses', 'totalAnnee', 'totauxParCategorie',
            'totalDepensesMois', 'totalDepotsMois', 'totalRetraitsMois',
            'categories', 'moisFiltre', 'anneeFiltre', 'libelleAnneeScolaire',
            'toutMois', 'moisParAnnee', 'periodeSelectionne'
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
            'type_mouvement' => 'required|in:depense,depot_banque,retrait_banque',
            'libelle'        => 'required|string|max:255',
            'montant'        => 'required|numeric|min:0.01',
            'categorie'      => 'required_if:type_mouvement,depense|nullable|in:fournitures,salaires,maintenance,transport,alimentation,autre',
            'date_depense'   => 'required|date',
            'annee_scolaire' => 'required|string|max:20',
            'beneficiaire'   => 'nullable|string|max:255',
            'description'    => 'nullable|string|max:1000',
        ]);

        if (($data['type_mouvement'] ?? 'depense') !== 'depense') {
            $data['categorie'] = null;
        }

        Depense::create($data);

        $msg = match($data['type_mouvement']) {
            'depot_banque'   => 'Dépôt bancaire enregistré.',
            'retrait_banque' => 'Retrait bancaire enregistré.',
            default          => 'Dépense enregistrée avec succès.',
        };

        return redirect()->route('depenses.index')->with('succes', $msg);
    }

    public function edit(Depense $depense)
    {
        return view('depenses.edit', compact('depense'));
    }

    public function update(Request $request, Depense $depense)
    {
        $data = $request->validate([
            'type_mouvement' => 'required|in:depense,depot_banque,retrait_banque',
            'libelle'        => 'required|string|max:255',
            'montant'        => 'required|numeric|min:0.01',
            'categorie'      => 'required_if:type_mouvement,depense|nullable|in:fournitures,salaires,maintenance,transport,alimentation,autre',
            'date_depense'   => 'required|date',
            'annee_scolaire' => 'required|string|max:20',
            'beneficiaire'   => 'nullable|string|max:255',
            'description'    => 'nullable|string|max:1000',
        ]);

        if (($data['type_mouvement'] ?? 'depense') !== 'depense') {
            $data['categorie'] = null;
        }

        $depense->update($data);

        return redirect()->route('depenses.index')->with('succes', 'Enregistrement mis à jour.');
    }

    public function destroy(Depense $depense)
    {
        $type = $depense->type_mouvement;
        $depense->delete();

        $msg = match($type) {
            'depot_banque'   => 'Dépôt bancaire supprimé.',
            'retrait_banque' => 'Retrait bancaire supprimé.',
            default          => 'Dépense supprimée.',
        };

        return redirect()->route('depenses.index')->with('succes', $msg);
    }

    public function exportPdf(Request $request)
    {
        $query = Depense::query();
        [$mois, $annee, $toutMois] = $this->periodeDemandee($request);

        $this->filtrerSurPeriode($query, 'date_depense', $mois, $annee, $toutMois);

        if ($request->filled('type_mouvement')) {
            $query->where('type_mouvement', $request->type_mouvement);
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        $depenses = $query->orderByDesc('date_depense')->get();
        $total = $depenses->sum('montant');
        $etablissement = Etablissement::first();
        $libelleAnnee = AnneeScolaire::libellePourDate(Carbon::create($annee, $mois, 1));
        $periode      = $toutMois
            ? 'Année scolaire ' . $libelleAnnee
            : Carbon::create($annee, $mois, 1)->locale('fr')->translatedFormat('F Y');

        $pdf = Pdf::loadView('exports.depenses-pdf', compact('depenses', 'total', 'etablissement', 'periode'))
            ->setPaper('a4', 'portrait');

        $suffixe = $this->suffixeFichierPeriode($mois, $annee, $toutMois);

        return $pdf->download('depenses_' . $suffixe . '.pdf');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Depense::query();
        [$mois, $annee, $toutMois] = $this->periodeDemandee($request);

        $this->filtrerSurPeriode($query, 'date_depense', $mois, $annee, $toutMois);

        if ($request->filled('type_mouvement')) {
            $query->where('type_mouvement', $request->type_mouvement);
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        $depenses = $query->orderByDesc('date_depense')->get();

        return response()->streamDownload(function () use ($depenses) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, ['Type', 'Libellé', 'Montant', 'Catégorie', 'Date', 'Bénéficiaire', 'Description'], ';');
            $typeLabels = ['depense' => 'Dépense', 'depot_banque' => 'Dépôt bancaire', 'retrait_banque' => 'Retrait bancaire'];
            foreach ($depenses as $d) {
                fputcsv($handle, [
                    $typeLabels[$d->type_mouvement] ?? $d->type_mouvement,
                    $d->libelle, $d->montant, $d->categorie ?? '',
                    $d->date_depense->format('d/m/Y'), $d->beneficiaire ?? '', $d->description ?? '',
                ], ';');
            }
            fclose($handle);
        }, 'depenses_' . $this->suffixeFichierPeriode($mois, $annee, $toutMois) . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
