<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ========================================
 * GUEST MODEL - GESTION DES INVITÉS
 * ========================================
 * 
 * Ce modèle représente un invité à un événement.
 * Il stocke les informations personnelles de l'invité et gère
 * son statut RSVP, ses choix de boissons et son invitation.
 */

class Guest extends Model
{
    protected $fillable = [
        'event_id', 'guest_type_id', 'event_table_id', 'first_name', 'last_name',
        'email', 'phone', 'rsvp_status', 'meal_choice', 'unique_url', 'response_date'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function guestType()
    {
        return $this->belongsTo(GuestType::class);
    }

    public function invitation()
    {
        return $this->hasOne(Invitation::class);
    }

    public function drinkChoices()
    {
        return $this->hasMany(GuestDrinkChoice::class);
    }

    public function eventTable()
    {
        return $this->belongsTo(EventTable::class);
    }

    public function guestBooks()
    {
        return $this->hasMany(GuestBook::class);
    }
}
