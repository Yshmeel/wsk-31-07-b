<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    public $fillable = [
        'event_id',
        'name'
    ];

    public $timestamps = false;

    public function event() {
        return $this->belongsTo(Event::class);
    }

    public function rooms() {
        return $this->hasMany(Room::class)->with(['sessions']);
    }

    public function sessions() {
        $i = 0;

        foreach($this->rooms as $room) {
            $i += count($room->sessions);
        }

        return $i;
    }

    public function roomsCount() {
        return $this->rooms()->count();
    }
}
