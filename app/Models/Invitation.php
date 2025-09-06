<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'unique_code',
        'event_date',
        'invitation_url',
        'status',
        'sent_at',
        'opened_at',
        'event_id',
        'guest_id', // nécessaire
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * ✅ CORRECTION : Relation avec le contenu de l'invitation
     * Chaque invitation a un contenu spécifique
     */
    public function content()
    {
        return $this->hasOne(Content::class, 'invitation_id');
    }

    /**
     * ✅ CORRECTION : Relation avec l'invité
     * Chaque invitation est liée à un invité spécifique
     */
    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    /**
     * ✅ SUPPRESSION : Cette relation n'est pas cohérente
     * Une invitation ne peut pas avoir plusieurs invités
     */
    // public function guests()
    // {
    //     return $this->hasMany(Guest::class, 'invitation_id');
    // }
}
