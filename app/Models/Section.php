<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = ['nom', 'couleur', 'niveau', 'ordre'];

    public function matieres(): HasMany
    {
        return $this->hasMany(Matiere::class, 'section', 'nom');
    }

    public function scopeOrdonnes($query)
    {
        return $query->orderBy('ordre')->orderBy('nom');
    }

    public function scopeForNiveau($query, ?string $niveau)
    {
        return $query->where(function ($q) use ($niveau) {
            $q->whereNull('niveau')->orWhere('niveau', '');
            if ($niveau) {
                $q->orWhere('niveau', $niveau);
            }
        });
    }
}
