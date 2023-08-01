<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizer extends \Illuminate\Foundation\Auth\User
{
    use HasFactory, Authenticatable;

    public $fillable = [
        'name',
        'slug',
        'email',
        'password_hash'
    ];
}
