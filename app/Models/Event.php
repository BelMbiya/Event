<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'organizer_id', 'event_type_id', 'title', 'description', 'event_date', 'location', 
        'google_maps_url', 'program', 'theme_color'
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function eventDrinks()
    {
        return $this->hasMany(EventDrink::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    /**
     * ✅ NOUVEAU : Vérifier si l'événement a des invitations
     */
    public function hasInvitations()
    {
        return $this->invitations()->exists();
    }

    /**
     * ✅ NOUVEAU : Vérifier si l'événement n'a pas d'invitations
     */
    public function hasNoInvitations()
    {
        return !$this->hasInvitations();
    }

    /**
     * ✅ NOUVEAU : Compter le nombre d'invitations
     */
    public function invitationsCount()
    {
        return $this->invitations()->count();
    }

    /**
     * ✅ NOUVEAU : Vérifier si l'événement a des invités
     */
    public function hasGuests()
    {
        return $this->guests()->exists();
    }

    /**
     * ✅ NOUVEAU : Vérifier si l'événement n'a pas d'invités
     */
    public function hasNoGuests()
    {
        return !$this->hasGuests();
    }

    /**
     * ✅ NOUVEAU : Compter le nombre d'invités
     */
    public function guestsCount()
    {
        return $this->guests()->count();
    }

    /**
     * ✅ NOUVEAU : Obtenir le statut des invitations de l'événement
     */
    public function getInvitationsStatus()
    {
        $invitationsCount = $this->invitationsCount();
        $guestsCount = $this->guestsCount();
        
        if ($guestsCount === 0) {
            return [
                'status' => 'no_guests',
                'message' => 'Aucun invité ajouté à cet événement',
                'invitations_count' => 0,
                'guests_count' => 0,
                'can_create_invitations' => false
            ];
        }
        
        if ($invitationsCount === 0) {
            return [
                'status' => 'no_invitations',
                'message' => 'Aucune invitation créée pour cet événement',
                'invitations_count' => 0,
                'guests_count' => $guestsCount,
                'can_create_invitations' => true
            ];
        }
        
        return [
            'status' => 'has_invitations',
            'message' => "{$invitationsCount} invitation(s) créée(s) pour {$guestsCount} invité(s)",
            'invitations_count' => $invitationsCount,
            'guests_count' => $guestsCount,
            'can_create_invitations' => $invitationsCount < $guestsCount
        ];
    }
}
