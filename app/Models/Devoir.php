<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devoir extends Model
{
    protected $fillable = [
        'intitule', 'matiere_id', 'classe_id', 'date_devoir',
        'note_max', 'description', 'annee_scolaire', 'trimestre',
    ];

    protected $casts = [
        'date_devoir' => 'date',
        'note_max' => 'decimal:2',
    ];

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function getMoyenneAttribute(): float
    {
        return $this->notes()->avg('note') ?? 0;
    }
}
