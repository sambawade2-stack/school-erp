<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AnneeScolaire extends Model
{
    protected $table = 'annees_scolaires';

    protected $fillable = ['libelle', 'date_debut', 'date_fin', 'statut', 'bulletins_ouverts', 'trimestre_actuel'];

    protected $casts = [
        'date_debut'        => 'date',
        'date_fin'          => 'date',
        'bulletins_ouverts' => 'boolean',
    ];

    /** Périodes disponibles selon le cycle scolaire */
    const PERIODES_TRIMESTRE = ['T1', 'T2', 'T3'];
    const PERIODES_SEMESTRE  = ['S1', 'S2', 'S3'];
    const TOUTES_PERIODES    = ['T1', 'T2', 'T3', 'S1', 'S2', 'S3'];

    /** Labels lisibles par période */
    const LABELS_PERIODES = [
        'T1' => '1er Trimestre', 'T2' => '2e Trimestre', 'T3' => '3e Trimestre',
        'S1' => '1er Semestre',  'S2' => '2e Semestre',  'S3' => '3e Semestre',
    ];

    public static function active(): ?self
    {
        return static::where('statut', 'en_cours')->first();
    }

    public static function libelleActif(): string
    {
        return static::active()?->libelle
            ?? static::latest()->value('libelle')
            ?? now()->year . '-' . (now()->year + 1);
    }

    public static function trimestreActif(): string
    {
        return static::active()?->trimestre_actuel ?? 'T1';
    }

    /**
     * Année scolaire couvrant une date donnée : une année scolaire étant à cheval
     * sur deux années civiles, décembre 2025 appartient par exemple à 2025-2026.
     */
    public static function pourDate($date): ?self
    {
        $jour = $date instanceof Carbon ? $date->copy() : Carbon::parse($date);

        return static::whereNotNull('date_debut')
            ->whereNotNull('date_fin')
            ->whereDate('date_debut', '<=', $jour)
            ->whereDate('date_fin', '>=', $jour)
            ->orderByDesc('date_debut')
            ->first();
    }

    /**
     * Bornes [début, fin] de l'année scolaire couvrant une date.
     * Si aucune année enregistrée ne couvre la date, on retombe sur une année type
     * démarrant au mois de rentrée de l'année active (septembre par défaut).
     */
    public static function bornesPourDate($date): array
    {
        $jour = $date instanceof Carbon ? $date->copy() : Carbon::parse($date);

        if ($annee = static::pourDate($jour)) {
            return [$annee->date_debut->copy()->startOfDay(), $annee->date_fin->copy()->endOfDay()];
        }

        $moisRentree = static::active()?->date_debut?->month ?? 9;
        $anneeDebut  = $jour->month >= $moisRentree ? $jour->year : $jour->year - 1;
        $debut       = Carbon::create($anneeDebut, $moisRentree, 1)->startOfDay();

        return [$debut, $debut->copy()->addYear()->subDay()->endOfDay()];
    }

    /** Libellé de l'année scolaire couvrant une date (ex : "2025-2026"). */
    public static function libellePourDate($date): string
    {
        if ($annee = static::pourDate($date)) {
            return $annee->libelle;
        }

        [$debut, $fin] = static::bornesPourDate($date);

        return $debut->year . '-' . $fin->year;
    }

    /**
     * Indique si une période est un semestre (S1/S2/S3) ou un trimestre (T1/T2/T3).
     */
    public static function estSemestre(string $periode): bool
    {
        return str_starts_with($periode, 'S');
    }

    /**
     * Retourne le label "Trimestre" ou "Semestre" selon le préfixe de la période.
     */
    public static function labelPeriode(string $periode): string
    {
        return static::estSemestre($periode) ? 'Semestre' : 'Trimestre';
    }

    /**
     * Retourne le numéro ordinal de la période (ex : "1er", "2e", "3e").
     */
    public static function ordinalPeriode(string $periode): string
    {
        $num = (int) substr($periode, 1);
        return match($num) {
            1       => '1<sup>er</sup>',
            default => $num . '<sup>e</sup>',
        };
    }

    public function getEstOuverteAttribute(): bool
    {
        return $this->statut === 'en_cours' || $this->bulletins_ouverts;
    }
}
