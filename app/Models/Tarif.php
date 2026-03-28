<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $fillable = ['annee_scolaire', 'niveau', 'type_frais', 'libelle', 'montant'];

    protected $casts = ['montant' => 'decimal:2'];

    public const TYPES = [
        'premiere_mensualite' => '1ère Mensualité',
        'avance_juillet'      => 'Avance Juillet',
        'tenues'              => 'Tenues',
        'assurance_maladie'   => 'Assurance Maladie',
        'mensualite'          => 'Mensualité',
        'inscription'         => 'Inscription',
    ];

    public const TYPE_COLORS = [
        'premiere_mensualite' => ['color' => '#2563eb', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
        'avance_juillet'      => ['color' => '#0891b2', 'bg' => '#ecfeff', 'border' => '#a5f3fc'],
        'tenues'              => ['color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a'],
        'assurance_maladie'   => ['color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#ddd6fe'],
        'mensualite'          => ['color' => '#1d4ed8', 'bg' => '#eff6ff', 'border' => '#93c5fd'],
        'inscription'         => ['color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'],
    ];

    public const NIVEAUX = [
        'elementaire' => 'Élémentaire',
        'college'     => 'Collège',
        'terminal'    => 'Terminal',
    ];

    /** Types regroupés dans le bloc "Inscription" */
    public const GROUPE_INSCRIPTION = ['premiere_mensualite', 'avance_juillet', 'tenues', 'assurance_maladie'];
}
