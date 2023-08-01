<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionRegistration extends Model
{
    use HasFactory;

    public $fillable = [
        'registration_id',
        'session_id'
    ];

    public $timestamps = false;

    public function registration() {
        return $this->belongsTo(Registration::class);
    }

    public function session() {
        return $this->belongsTo(Session::class);
    }
}
