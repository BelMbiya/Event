<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ========================================
 * EVENT TABLE MODEL - GESTION DES TABLES D'ÉVÉNEMENTS
 * ========================================
 * 
 * Ce modèle gère l'organisation des tables pour les événements.
 * Il permet de planifier la disposition des invités en tables
 * pour l'organisation de la salle et du service.
 */
class EventTable extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'table_number',
        'capacity'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class, 'event_table_id');
    }
}
