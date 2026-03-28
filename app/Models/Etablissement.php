<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    protected $fillable = [
        'nom', 'sigle', 'adresse', 'telephone', 'email', 'directeur',
        'logo', 'description', 'pays', 'ville', 'code_postal', 'jour_limite_paiement',
    ];
}
