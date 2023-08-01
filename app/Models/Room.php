<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public $fillable = [
        'channel_id',
        'name',
        'capacity'
    ];

    public $timestamps = false;

    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    public function sessions() {
        return $this->hasMany(Session::class);
    }
}
