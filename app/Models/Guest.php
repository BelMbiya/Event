<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
