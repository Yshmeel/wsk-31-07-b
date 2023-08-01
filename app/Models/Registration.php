<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    public $fillable = [
        'attendee_id',
        'ticket_id',
        'registration_time'
    ];

    public $timestamps = false;

    public function attendee() {
        return $this->belongsTo(Attendee::class);
    }

    public function ticket() {
        return $this->belongsTo(EventTicket::class);
    }
}
