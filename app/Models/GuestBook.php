<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
