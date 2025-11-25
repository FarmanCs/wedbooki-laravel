<?php

namespace App\Models\Vendor;

use App\Models\Host\Host;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_booking_id',
        'host_id',
        'venue_id',
        'business_id',
        'package_id',
        'event_date',
        'start_time',
        'end_time',
        'timezone',
        'time_slot',
        'amount',
        'advance_amount',
        'final_amount',
        'advance_percentage',
        'advance_due_date',
        'final_due_date',
        'extra_services',
        'status',
        'payment_status',
        'approved_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'approved_at' => 'datetime',
        'advance_due_date' => 'date',
        'final_due_date' => 'date',
        'extra_services' => 'array',
    ];

    public function host()
    {
        return $this->belongsTo(Host::class, 'host_id');
    }

    public function venue()
    {
        return $this->belongsTo(Vendor::class, 'venue_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
