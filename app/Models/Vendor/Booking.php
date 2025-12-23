<?php

namespace App\Models\Vendor;

use App\Models\Host\Host;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bookings';

    protected $fillable = [
        'host_id',
        'business_id',
        'vendor_id',
        'package_id',
        'amount',
        'advance_percentage',
        'advance_amount',
        'final_amount',
        'advance_due_date',
        'final_due_date',
        'event_date',
        'timezone',
        'time_slot',
        'guests',
        'custom_booking_id',
        'status',
        'start_time',
        'end_time',
        'approved_at',
        'payment_completed_at',
        'payment_status',
        'advance_paid',
        'final_paid',
        'is_synced_with_calendar',
    ];

    protected $casts = [
        'event_date' => 'date',
        'advance_due_date' => 'date',
        'final_due_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'approved_at' => 'datetime',
        'payment_completed_at' => 'datetime',
        'advance_paid' => 'boolean',
        'final_paid' => 'boolean',
        'is_synced_with_calendar' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Vendor (venue) relationship
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id'); // migration column is venue_id
    }

    // Business relationship
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    // Package relationship
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    // Host relationship
    public function host()
    {
        return $this->belongsTo(Host::class, 'host_id');
    }

    // Extra services linked to this booking
    public function extra_services()
    {
        return $this->hasMany(ExtraService::class, 'booking_id');
    }
}
