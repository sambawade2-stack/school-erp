<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enseignant extends Model
{
    protected $fillable = [
        'prenom', 'nom', 'type', 'email', 'telephone', 'adresse',
        'specialite', 'photo', 'date_embauche', 'statut',
    ];

    public const TYPES = [
        'enseignant'  => 'Enseignant',
        'surveillant' => 'Surveillant',
        'comptable'   => 'Comptable',
        'secretaire'  => 'Secrétaire',
        'chauffeur'   => 'Chauffeur',
        'cuisinier'   => 'Cuisinier(ère)',
        'gardien'     => 'Gardien',
        'autre'       => 'Autre',
    ];

    protected $casts = [
        'date_embauche' => 'date',
    ];

    public function matieres(): HasMany
    {
        return $this->hasMany(Matiere::class);
    }

    public function classesResponsable(): HasMany
    {
        return $this->hasMany(Classe::class, 'enseignant_id');
    }

    public function emploisDuTemps(): HasMany
    {
        return $this->hasMany(EmploiDuTemps::class);
    }

    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return route('enseignants.photo', $this);
        }

        return '';
    }
}
