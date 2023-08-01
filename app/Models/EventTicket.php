<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    use HasFactory;

    public $fillable = [
        'event_id',
        'name',
        'cost',
        'special_validity'
    ];

    public $timestamps = false;

    public function event() {
        return $this->belongsTo(Event::class);
    }

    public function registrations() {
        return $this->hasMany(Registration::class, 'ticket_id');
    }

    public function isTicketAvailable() {
        $arr = json_decode($this->special_validity, true);

        if(!is_array($arr) || !isset($arr['type'])) {
            return true;
        }

        if($arr['type'] == 'amount') {
            return $arr['amount'] >= $this->registrations()->count();
        } else {
            return strtotime($arr['date']) >= time();
        }
    }

    public function specialValidity() {
        $arr = json_decode($this->special_validity, true);

        if(!is_array($arr) || !isset($arr['type'])) {
            return '-';
        }

        switch($arr['type']) {
            case 'amount':
                return $arr['amount'] . ' tickets available';
            case 'date':
                // @todo fix
                return 'Available until ' .
                    date("M d, Y", strtotime($arr['date']));
        }
    }
}
