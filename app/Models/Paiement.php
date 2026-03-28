<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paiement extends Model
{
    protected $fillable = [
        'etudiant_id', 'montant', 'montant_total', 'statut', 'type_paiement',
        'date_paiement', 'annee_scolaire', 'trimestre',
        'numero_recu', 'numero_facture', 'remarque', 'lignes',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant'       => 'decimal:2',
        'montant_total' => 'decimal:2',
        'lignes'        => 'array',
    ];

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function tranches(): HasMany
    {
        return $this->hasMany(TranchePaiement::class)->orderBy('date_paiement');
    }

    public function getMontantRestantAttribute(): float
    {
        $total = $this->montant_total ?? $this->montant;
        return max(0, $total - $this->montant);
    }

    public function getPourcentagePaieAttribute(): int
    {
        $total = $this->montant_total ?? $this->montant;
        if ($total <= 0) return 100;
        return min(100, (int) round(($this->montant / $total) * 100));
    }
}
