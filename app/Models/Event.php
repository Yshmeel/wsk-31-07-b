<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    public $fillable = [
        'organizer_id',
        'name',
        'slug',
        'date'
    ];

    public $timestamps = false;

    public function tickets() {
        return $this->hasMany(EventTicket::class);
    }

    public function channels() {
        return $this->hasMany(Channel::class);
    }

    public function organizer() {
        return $this->belongsTo(Organizer::class);
    }

    public function registrationsCount() {
        $i = 0;

        foreach($this->tickets as $ticket) {
            $i += count($ticket->registrations);
        }

        return $i;
    }
}
