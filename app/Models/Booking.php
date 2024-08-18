<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Booking extends Model
{
    use HasFactory, Notifiable;

    // Define fillable attributes
    protected $fillable = [
        'fullname',
        'email',
        'phone_number',
        'address',
        'reservation_datetime',
        'kid_detail',
    ];

    // Cast JSON column to array
    protected $casts = [
        'reservation_datetime' => 'datetime',
        'kid_detail' => 'array',
    ];
}
