<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'timings',
        'extra_services',
        'images',
        'price',
        'street',
        'city',
        'state',
        'country',
        'postal_code',
        'capacity',
        'available_dates',
        'status',
    ];

    protected $casts = [
        'timings'         => 'array',
        'extra_services'  => 'array',
        'images'          => 'array',
        'available_dates' => 'array',
        'price'           => 'decimal:2',
        'capacity'        => 'integer',
    ];

    // Each Venue belongs to a single Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    // Each Venue may have many bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'venue_id');
    }
}

