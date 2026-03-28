<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chambre extends Model
{
    protected $fillable = ['numero', 'capacite', 'description'];

    public function internes(): HasMany
    {
        return $this->hasMany(Interne::class);
    }

    /** Nombre d'élèves actuellement actifs dans la chambre */
    public function getOccupationAttribute(): int
    {
        return $this->internes()->where('statut', 'actif')->count();
    }

    /** Places disponibles */
    public function getPlacesDisponiblesAttribute(): int
    {
        return max(0, $this->capacite - $this->occupation);
    }

    /** Vrai si la chambre est pleine */
    public function getPleineAttribute(): bool
    {
        return $this->places_disponibles === 0;
    }
}
