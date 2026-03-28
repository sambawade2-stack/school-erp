<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationType extends Model
{
    protected $fillable = ['nom', 'slug', 'poids', 'couleur', 'description'];

    protected $casts = [
        'poids' => 'decimal:2',
    ];

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Poids formaté en pourcentage : 0.40 → "40%"
     */
    public function getPourcentageAttribute(): string
    {
        return round($this->poids * 100) . '%';
    }

    /**
     * Retourne tous les types avec leur poids total pour validation.
     * La somme des poids doit être proche de 1.0.
     */
    public static function sommePoids(): float
    {
        return (float) static::sum('poids');
    }

    /**
     * Trouve un type par son slug (ex: 'examen', 'devoir', 'composition').
     */
    public static function parSlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
