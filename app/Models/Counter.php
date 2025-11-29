<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    // Mass assignable fields
    protected $fillable = [
        'name',
        'seq',
    ];

    /**
     * Example: If a counter is related to multiple bookings
     * Uncomment this if you want to define a relationship
     */
    // public function bookings()
    // {
    //     return $this->hasMany(Booking::class, 'counter_id', 'id');
    // }
}
