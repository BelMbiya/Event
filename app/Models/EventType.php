<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ========================================
 * EVENT TYPE MODEL - GESTION DES TYPES D'ÉVÉNEMENTS
 * ========================================
 * 
 * Ce modèle définit les types d'événements disponibles (mariage, anniversaire, etc.).
 * Il sert de référence pour catégoriser les événements et appliquer
 * des configurations spécifiques selon le type.
 */

class EventType extends Model
{
    protected $fillable = [
        'label'
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
