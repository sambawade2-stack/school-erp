<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    protected $fillable = [
        'type_mouvement', 'libelle', 'montant', 'categorie', 'date_depense',
        'annee_scolaire', 'beneficiaire', 'description',
    ];

    protected $casts = [
        'date_depense' => 'date',
        'montant'      => 'decimal:2',
    ];
}
