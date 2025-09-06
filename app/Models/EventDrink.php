<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
