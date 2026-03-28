<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Matiere extends Model
{
    protected $fillable = ['nom', 'code', 'coefficient', 'niveau', 'enseignant_id', 'classe_id', 'section'];

    protected $casts = [
        'coefficient' => 'decimal:2',
    ];

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(Enseignant::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function examens(): HasMany
    {
        return $this->hasMany(Examen::class);
    }

    public function devoirs(): HasMany
    {
        return $this->hasMany(Devoir::class);
    }

    public function compositions(): HasMany
    {
        return $this->hasMany(Composition::class);
    }

    /**
     * Calcule la moyenne d'un étudiant pour cette matière sur une période.
     *
     * — Élémentaire : seules les compositions comptent (poids = 1.0).
     *   Les périodes sont des semestres (S1/S2/S3), mais le champ `trimestre`
     *   de la table compositions stocke la valeur telle quelle (S1/S2/S3).
     *
     * — Collège / Lycée : pondération par EvaluationType (Devoir, Compo, Examen).
     *   Les types absents sont renormalisés.
     *
     * @return float  Moyenne pondérée sur 20
     */
    public function moyenneEtudiant(int $etudiantId, string $anneeScolaire, string $trimestre): float
    {
        $categorie = $this->classe?->categorie ?? 'college';

        // ── ÉLÉMENTAIRE : composition uniquement ─────────────────────────────
        if ($categorie === 'elementaire') {
            $notes = Note::where('etudiant_id', $etudiantId)
                ->where('type', 'composition')
                ->whereHas('composition', fn($q) => $q->where('matiere_id', $this->id)
                    ->where('annee_scolaire', $anneeScolaire)
                    ->where('trimestre', $trimestre))
                ->pluck('note');

            return $notes->isNotEmpty() ? round((float) $notes->avg(), 2) : 0.0;
        }

        // ── SECONDAIRE : pondération par EvaluationType ───────────────────────
        $evalTypes = Cache::remember('eval_types_by_slug', 3600, fn() => EvaluationType::all())->keyBy('slug');

        if ($evalTypes->isEmpty()) {
            return $this->moyenneSimple($etudiantId, $anneeScolaire, $trimestre);
        }

        $notesParType = [];

        $examNotes = Note::where('etudiant_id', $etudiantId)->where('type', 'examen')
            ->whereHas('examen', fn($q) => $q->where('matiere_id', $this->id)
                ->where('annee_scolaire', $anneeScolaire)->where('trimestre', $trimestre))
            ->pluck('note');
        if ($examNotes->isNotEmpty()) $notesParType['examen'] = (float) $examNotes->avg();

        $devoirNotes = Note::where('etudiant_id', $etudiantId)->where('type', 'devoir')
            ->whereHas('devoir', fn($q) => $q->where('matiere_id', $this->id)
                ->where('annee_scolaire', $anneeScolaire)->where('trimestre', $trimestre))
            ->pluck('note');
        if ($devoirNotes->isNotEmpty()) $notesParType['devoir'] = (float) $devoirNotes->avg();

        $compNotes = Note::where('etudiant_id', $etudiantId)->where('type', 'composition')
            ->whereHas('composition', fn($q) => $q->where('matiere_id', $this->id)
                ->where('annee_scolaire', $anneeScolaire)->where('trimestre', $trimestre))
            ->pluck('note');
        if ($compNotes->isNotEmpty()) $notesParType['composition'] = (float) $compNotes->avg();

        if (empty($notesParType)) return 0.0;

        $totalPoints = 0.0;
        $totalPoids  = 0.0;

        foreach ($notesParType as $slug => $avg) {
            $poids        = (float) ($evalTypes->get($slug)?->poids ?? 1.0);
            $totalPoints += $avg * $poids;
            $totalPoids  += $poids;
        }

        return $totalPoids > 0 ? round($totalPoints / $totalPoids, 2) : 0.0;
    }

    /**
     * Moyenne simple (sans pondération par type), utilisée en fallback.
     */
    private function moyenneSimple(int $etudiantId, string $anneeScolaire, string $trimestre): float
    {
        $allNotes = collect();

        $allNotes = $allNotes->merge(
            Note::where('etudiant_id', $etudiantId)->where('type', 'examen')
                ->whereHas('examen', fn($q) => $q->where('matiere_id', $this->id)
                    ->where('annee_scolaire', $anneeScolaire)->where('trimestre', $trimestre))
                ->pluck('note')
        );
        $allNotes = $allNotes->merge(
            Note::where('etudiant_id', $etudiantId)->where('type', 'devoir')
                ->whereHas('devoir', fn($q) => $q->where('matiere_id', $this->id)
                    ->where('annee_scolaire', $anneeScolaire)->where('trimestre', $trimestre))
                ->pluck('note')
        );
        $allNotes = $allNotes->merge(
            Note::where('etudiant_id', $etudiantId)->where('type', 'composition')
                ->whereHas('composition', fn($q) => $q->where('matiere_id', $this->id)
                    ->where('annee_scolaire', $anneeScolaire)->where('trimestre', $trimestre))
                ->pluck('note')
        );

        return $allNotes->isNotEmpty() ? round($allNotes->avg(), 2) : 0.0;
    }

    public function getMentionAttribute(): string
    {
        return match(true) {
            $this->coefficient >= 4 => 'Principale',
            $this->coefficient >= 3 => 'Importante',
            $this->coefficient >= 2 => 'Standard',
            default                 => 'Mineure',
        };
    }
}
