<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Examen extends Model
{
    protected $fillable = [
        'intitule', 'matiere_id', 'classe_id',
        'date_examen', 'note_max', 'annee_scolaire', 'trimestre', 'description',
    ];

    protected $casts = [
        'date_examen' => 'date',
    ];

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function getMoyenneAttribute(): float
    {
        $count = $this->notes()->count();
        if ($count === 0) return 0;
        return round($this->notes()->avg('note'), 2);
    }
}
