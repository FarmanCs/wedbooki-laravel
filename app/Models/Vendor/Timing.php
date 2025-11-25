<?php

namespace App\Models\Vendor;

use App\Models\Vendor\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timing extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'slot_duration',
        'working_hours',
        'timings_venue',
        'timings_service_weekly',
        'unavailable_dates',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'timings_venue' => 'array',
        'timings_service_weekly' => 'array',
        'unavailable_dates' => 'array',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
