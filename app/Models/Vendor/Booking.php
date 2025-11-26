<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'business_id',
        'vendor_id',
        'package_id',
        'amount',
        'event_date',
        'time_slot',
        'custom_booking_id',
        'timezone',
        'guests',
        'start_time',
        'end_time',
        'extra_services',
        'advance_percentage',
        'advance_amount',
        'final_amount',
        'advance_due_date',
        'final_due_date',
        'status',
    ];

    // Relationships

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
//    public function vendor() { return $this->belongsTo(Vendor::class); }
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function host()
    {
        return $this->belongsTo(\App\Models\Host\Host::class, 'host_id');
    }
    public function extra_services(){
        return $this->hasMany('extra_services', 'extra_services_id');
    }
}
