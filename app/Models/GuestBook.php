<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ========================================
 * GUEST BOOK MODEL - GESTION DU LIVRE D'OR
 * ========================================
 * 
 * Ce modèle gère les messages laissés par les invités dans le livre d'or.
 * Il stocke les messages avec leur visibilité et les associe
 * à l'événement et à l'invité qui les a écrits.
 */

class GuestBook extends Model
{
    protected $fillable = [
        'event_id', 'guest_id', 'message', 'visibility'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
