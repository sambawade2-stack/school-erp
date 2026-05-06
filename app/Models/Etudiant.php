<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Etudiant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'matricule', 'prenom', 'nom', 'date_naissance', 'sexe',
        'photo', 'adresse', 'telephone', 'nom_parent', 'tel_parent',
        'classe_id', 'date_inscription', 'statut', 'regime_paiement',
    ];

    protected $casts = [
        'date_naissance'   => 'date',
        'date_inscription' => 'date',
    ];

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    public function internes(): HasMany
    {
        return $this->hasMany(Interne::class);
    }

    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return route('etudiants.photo', $this);
        }

        return '';
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance?->age;
    }
}
