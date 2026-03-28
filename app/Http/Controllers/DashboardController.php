<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Etablissement;
use App\Models\Etudiant;
use App\Models\Paiement;
use App\Models\Tarif;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $statistiques = [
            'etudiants'      => Etudiant::where('statut', 'actif')->count(),
            'enseignants'    => Enseignant::where('statut', 'actif')->count(),
            'classes'        => Classe::count(),
            'paiements_mois' => Paiement::whereMonth('date_paiement', now()->month)
                ->whereYear('date_paiement', now()->year)
                ->sum('montant'),
        ];

        $derniers_etudiants = Etudiant::with('classe')
            ->latest()
            ->take(5)
            ->get();

        $derniers_paiements = Paiement::with('etudiant')
            ->latest('date_paiement')
            ->take(3)
            ->get();

        // Paiements en retard : utilise le trimestre et l'année configurés par l'admin
        $trimestreCourant = AnneeScolaire::trimestreActif();
        $anneeScolaire = AnneeScolaire::libelleActif();

        $retardQuery = Etudiant::where('statut', 'actif')
            // Exclure les élèves inscrits ce mois-ci (leur mensualité commence le mois prochain)
            ->whereRaw("date_inscription IS NULL OR DATE_FORMAT(date_inscription, '%Y-%m') < DATE_FORMAT(NOW(), '%Y-%m')")
            ->whereDoesntHave('paiements', function ($q) use ($trimestreCourant, $anneeScolaire) {
                $q->where('type_paiement', 'mensualite')
                  ->where('annee_scolaire', $anneeScolaire)
                  ->where(function ($sq) use ($trimestreCourant) {
                      $sq->where('trimestre', $trimestreCourant)
                         ->orWhereNull('trimestre');
                  });
            });

        $nb_impayes = $retardQuery->count();
        $etudiants_en_retard = collect(); // Plus affiché en tableau sur le dashboard

        // Alarme paiement : élèves en retard si la date limite est dépassée ce mois-ci
        $etablissement = Etablissement::first();
        $jourLimite = $etablissement?->jour_limite_paiement;
        $alarme_paiement = null;

        if ($jourLimite && now()->day > $jourLimite) {
            $moisCourant = now()->month;
            $anneeCourante = now()->year;

            $nbEnRetard = Etudiant::where('statut', 'actif')
                // Exclure les élèves inscrits ce mois-ci (leur mensualité commence le mois prochain)
                ->whereRaw("date_inscription IS NULL OR DATE_FORMAT(date_inscription, '%Y-%m') < DATE_FORMAT(NOW(), '%Y-%m')")
                ->whereDoesntHave('paiements', function ($q) use ($moisCourant, $anneeCourante) {
                    $q->where('type_paiement', 'mensualite')
                      ->whereMonth('date_paiement', $moisCourant)
                      ->whereYear('date_paiement', $anneeCourante);
                })
                ->count();

            if ($nbEnRetard > 0) {
                $alarme_paiement = [
                    'nb'          => $nbEnRetard,
                    'jour_limite' => $jourLimite,
                    'mois'        => now()->locale('fr')->isoFormat('MMMM YYYY'),
                ];
            }
        }

        // Statistiques mensuelles pour graphique (2 requêtes au lieu de 24)
        $annee = now()->year;
        $etudiantsParMois = Etudiant::selectRaw("DATE_FORMAT(created_at, '%m') as m, COUNT(*) as total")
            ->whereYear('created_at', $annee)
            ->groupBy('m')
            ->pluck('total', 'm');
        $paiementsParMois = Paiement::selectRaw("DATE_FORMAT(date_paiement, '%m') as m, SUM(montant) as total")
            ->whereYear('date_paiement', $annee)
            ->groupBy('m')
            ->pluck('total', 'm');
        $mois = collect(range(1, 12))->map(fn($m) => [
            'mois'      => $m,
            'etudiants' => $etudiantsParMois[str_pad($m, 2, '0', STR_PAD_LEFT)] ?? 0,
            'paiements' => $paiementsParMois[str_pad($m, 2, '0', STR_PAD_LEFT)] ?? 0,
        ]);

        return view('dashboard.index', compact(
            'statistiques', 'derniers_etudiants', 'derniers_paiements', 'mois',
            'nb_impayes', 'trimestreCourant', 'anneeScolaire', 'alarme_paiement'
        ));
    }

    public function impayes(Request $request)
    {
        $trimestreCourant = $request->trimestre ?? AnneeScolaire::trimestreActif();
        $anneeScolaire = $request->annee_scolaire ?? AnneeScolaire::libelleActif();

        $classes = Classe::orderBy('nom')->get();

        $query = Etudiant::with('classe')
            ->where('statut', 'actif')
            // Exclure les élèves inscrits ce mois-ci (leur mensualité commence le mois prochain)
            ->whereRaw("date_inscription IS NULL OR DATE_FORMAT(date_inscription, '%Y-%m') < DATE_FORMAT(NOW(), '%Y-%m')")
            ->whereDoesntHave('paiements', function ($q) use ($trimestreCourant, $anneeScolaire) {
                $q->where('type_paiement', 'mensualite')
                  ->where('annee_scolaire', $anneeScolaire)
                  ->where(function ($sq) use ($trimestreCourant) {
                      $sq->where('trimestre', $trimestreCourant)
                         ->orWhereNull('trimestre');
                  });
            });

        if ($request->filled('classe_id')) {
            if ($request->classe_id === 'sans_classe') {
                $query->whereNull('classe_id');
            } else {
                $query->where('classe_id', $request->classe_id);
            }
        }

        $etudiants = $query->orderBy('nom')->get();

        // Tarifs mensualité par niveau pour l'année active
        $tarifsScolarite = Tarif::where('annee_scolaire', $anneeScolaire)
            ->where('type_frais', 'mensualite')
            ->pluck('montant', 'niveau');

        return view('dashboard.impayes', compact(
            'etudiants', 'classes', 'trimestreCourant', 'anneeScolaire', 'tarifsScolarite'
        ));
    }
}
