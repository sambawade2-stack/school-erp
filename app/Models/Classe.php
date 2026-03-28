<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    protected $fillable = ['nom', 'niveau', 'categorie', 'capacite', 'annee_scolaire', 'description', 'enseignant_id'];

    const CATEGORIES = [
        'elementaire' => 'Élémentaire',
        'college'     => 'Collège',
        'lycee'       => 'Lycée',
    ];

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Enseignant::class, 'enseignant_id');
    }

    public function etudiants(): HasMany
    {
        return $this->hasMany(Etudiant::class, 'classe_id');
    }

    public function matieres(): HasMany
    {
        return $this->hasMany(Matiere::class, 'classe_id');
    }

    public function emploisDuTemps(): HasMany
    {
        return $this->hasMany(EmploiDuTemps::class, 'classe_id');
    }

    public function examens(): HasMany
    {
        return $this->hasMany(Examen::class, 'classe_id');
    }

    public function getNombreEtudiantsAttribute(): int
    {
        return $this->etudiants()->where('statut', 'actif')->count();
    }
}
