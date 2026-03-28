<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranchePaiement extends Model
{
    protected $table = 'tranches_paiement';

    protected $fillable = ['paiement_id', 'montant', 'date_paiement', 'numero_recu', 'remarque'];

    protected $casts = [
        'date_paiement' => 'date',
        'montant'       => 'decimal:2',
    ];

    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }
}
