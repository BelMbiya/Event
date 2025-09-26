<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ========================================
 * EVENT DRINK MODEL - GESTION DES BOISSONS D'ÉVÉNEMENT
 * ========================================
 * 
 * Ce modèle fait le lien entre un événement et ses boissons disponibles.
 * Il permet de personnaliser les prix et la disponibilité des boissons
 * pour chaque événement spécifique.
 */

class EventDrink extends Model
{
    protected $fillable = [
        'event_id',
        'drink_id',
        'price',
        'available',
        'limit_per_guest',
        'display_order',
    ];

    public function event()
{
    return $this->belongsTo(Event::class);
}

public function guestChoices()
{
    return $this->hasMany(GuestDrinkChoice::class);
}

    public function drink()
    {
        return $this->belongsTo(Drink::class, 'drink_id');
    }

}
