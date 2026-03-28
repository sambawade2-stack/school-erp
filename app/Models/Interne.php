<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interne extends Model
{
    protected $fillable = [
        'etudiant_id', 'chambre_id', 'chambre', 'date_entree', 'date_sortie',
        'annee_scolaire', 'statut', 'remarque',
    ];

    protected $casts = [
        'date_entree' => 'date',
        'date_sortie' => 'date',
    ];

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function chambreObj(): BelongsTo
    {
        return $this->belongsTo(Chambre::class, 'chambre_id');
    }
}
