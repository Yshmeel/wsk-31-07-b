<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendee extends \Illuminate\Foundation\Auth\User
{
    use HasFactory;

    public  $fillable = [
        'id',
        'firstname',
        'lastname',
        'username',
        'email',
        'registration_code',
        'login_token'
    ];

    public $timestamps = false;

    public function registrations() {
        return $this->hasMany(Registration::class);
    }

}
