<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ========================================
 * GUEST DRINK CHOICE MODEL - GESTION DES CHOIX DE BOISSONS
 * ========================================
 * 
 * Ce modèle enregistre les choix de boissons des invités.
 * Il stocke la quantité et les commentaires pour chaque boisson
 * sélectionnée par un invité lors de sa réponse RSVP.
 */

class GuestDrinkChoice extends Model
{
    //
    protected $fillable = ['guest_id', 'event_drink_id', 'quantity', 'comment'];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function eventDrink()
    {
        return $this->belongsTo(EventDrink::class, 'event_drink_id');
    }

}
