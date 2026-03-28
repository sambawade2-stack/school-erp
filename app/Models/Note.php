<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = [
        'examen_id', 'devoir_id', 'composition_id',
        'evaluation_type_id', 'etudiant_id', 'type', 'note', 'commentaire',
    ];

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class);
    }

    public function devoir(): BelongsTo
    {
        return $this->belongsTo(Devoir::class);
    }

    public function composition(): BelongsTo
    {
        return $this->belongsTo(Composition::class);
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function evaluationType(): BelongsTo
    {
        return $this->belongsTo(EvaluationType::class, 'evaluation_type_id');
    }

    /**
     * Matière liée (via examen, devoir ou composition).
     */
    public function getMatiereLieeAttribute(): ?Matiere
    {
        return $this->examen?->matiere
            ?? $this->devoir?->matiere
            ?? $this->composition?->matiere;
    }

    public function getMentionAttribute(): string
    {
        $note = $this->note;
        if ($note >= 16) return 'Très Bien';
        if ($note >= 14) return 'Bien';
        if ($note >= 12) return 'Assez Bien';
        if ($note >= 10) return 'Passable';
        return 'Insuffisant';
    }
}
