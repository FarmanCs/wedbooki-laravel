<?php

namespace App\Models\Vendor;

use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'price',
        'discount',
        'discount_percentage',
        'description',
        'features',
        'is_popular',
    ];

    protected $casts = [
        'features' => 'array',
        'is_popular' => 'boolean',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'package_id');
    }
}
