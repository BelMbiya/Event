<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
