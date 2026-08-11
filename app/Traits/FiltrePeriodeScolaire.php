<?php

namespace App\Traits;

use App\Models\AnneeScolaire;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Filtrage par période calé sur l'année scolaire, qui est à cheval sur deux
 * années civiles : décembre 2025 appartient à l'année scolaire 2025-2026 et doit
 * rester sélectionnable et comptabilisé après le passage au 1er janvier.
 *
 * Le sélecteur envoie un paramètre "periode" :
 *   - "2025-12"    → le mois de décembre 2025
 *   - "2025-2026"  → toute l'année scolaire 2025-2026
 */
trait FiltrePeriodeScolaire
{
    /**
     * Période demandée : [mois, année, toute l'année scolaire ?].
     * Les paramètres mois/annee restent acceptés (anciens liens, favoris).
     */
    protected function periodeDemandee(Request $request, bool $defautAnneeComplete = false): array
    {
        $periode = trim((string) $request->input('periode', ''));

        // Année scolaire complète ("2025-2026") : on se cale sur son mois de début.
        if (preg_match('/^(\d{4})-(\d{4})$/', $periode, $p)) {
            $debut = AnneeScolaire::where('libelle', $periode)->first()?->date_debut
                ?? Carbon::create(max(2000, min(2100, (int) $p[1])), 12, 1);

            return [$debut->month, $debut->year, true];
        }

        // Mois précis ("2025-12").
        if (preg_match('/^(\d{4})-(\d{1,2})$/', $periode, $p)) {
            return [max(1, min(12, (int) $p[2])), max(2000, min(2100, (int) $p[1])), false];
        }

        $mois  = $request->filled('mois')  ? max(1, min(12, (int) $request->mois))       : now()->month;
        $annee = $request->filled('annee') ? max(2000, min(2100, (int) $request->annee)) : now()->year;

        $aucunFiltre = ! $request->hasAny(['periode', 'mois', 'annee', 'tout']);
        $toutMois    = $request->boolean('tout')
            || ($request->has('periode') && $periode === '')
            || ($defautAnneeComplete && $aucunFiltre);

        return [$mois, $annee, $toutMois];
    }

    /**
     * Restreint une requête au mois demandé, ou à toute l'année scolaire qui le contient.
     */
    protected function filtrerSurPeriode($query, string $colonneDate, int $mois, int $annee, bool $toutMois)
    {
        if ($toutMois) {
            return $query->whereBetween($colonneDate, AnneeScolaire::bornesPourDate(Carbon::create($annee, $mois, 1)));
        }

        return $query->whereMonth($colonneDate, $mois)->whereYear($colonneDate, $annee);
    }

    /**
     * Suffixe de nom de fichier pour les exports : "12_2025" ou "annee_2025-2026".
     */
    protected function suffixeFichierPeriode(int $mois, int $annee, bool $toutMois): string
    {
        if ($toutMois) {
            return 'annee_' . AnneeScolaire::libellePourDate(Carbon::create($annee, $mois, 1));
        }

        return $mois . '_' . $annee;
    }

    /**
     * Options du sélecteur, groupées par année scolaire :
     * [['libelle' => '2025-2026', 'mois' => [['valeur' => '2025-12', 'label' => 'Décembre 2025'], …]], …].
     * Sans année enregistrée en base, on retombe sur l'année scolaire en cours.
     */
    protected function optionsPeriodeScolaire(Carbon $debutCourant, Carbon $finCourant, string $libelleCourant, string $periodeSelectionnee): array
    {
        $annees = AnneeScolaire::whereNotNull('date_debut')
            ->whereNotNull('date_fin')
            ->orderByDesc('date_debut')
            ->get()
            ->map(fn ($a) => [$a->libelle, $a->date_debut, $a->date_fin])
            ->all();

        if (empty($annees)) {
            $annees = [[$libelleCourant, $debutCourant, $finCourant]];
        }

        $groupes = [];

        foreach ($annees as [$libelle, $debut, $fin]) {
            $curseur = $debut->copy()->startOfMonth();
            $dernier = $fin->copy()->startOfMonth();
            $mois    = [];

            // Garde-fou : une année scolaire mal saisie ne doit pas générer une liste sans fin.
            while ($curseur <= $dernier && count($mois) < 18) {
                $mois[] = [
                    'valeur' => $curseur->format('Y-m'),
                    'label'  => ucfirst($curseur->locale('fr')->translatedFormat('F Y')),
                ];
                $curseur->addMonth();
            }

            $groupes[] = ['libelle' => $libelle, 'valeur_annee' => $libelle, 'mois' => $mois];
        }

        // Un mois hors des années enregistrées (vacances, année pas encore créée) doit
        // rester visible : sinon le sélecteur afficherait une période différente de la page.
        $connus = collect($groupes)->pluck('mois')->flatten(1)->pluck('valeur');

        if (preg_match('/^(\d{4})-(\d{2})$/', $periodeSelectionnee, $p) && ! $connus->contains($periodeSelectionnee)) {
            array_unshift($groupes, [
                'libelle'      => 'Hors année scolaire',
                'valeur_annee' => null,
                'mois'         => [[
                    'valeur' => $periodeSelectionnee,
                    'label'  => ucfirst(Carbon::create((int) $p[1], (int) $p[2], 1)->locale('fr')->translatedFormat('F Y')),
                ]],
            ]);
        }

        return $groupes;
    }
}
