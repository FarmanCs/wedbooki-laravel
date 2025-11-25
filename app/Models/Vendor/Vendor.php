<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Vendor extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone_no',
        'country_code',
        'country',
        'city',
        'password',
        'category',
        'custom_vendor_id',
        'business_profile_id',
        'profile_image',
        'cover_image',
        'about',
        'years_of_experince',
        'team_members',
        'languages',
        'specialties',
        'otp',
        'pending_email',
        'google_id',
        'apple_id',
        'signup_method',
        'email_verified',
        'is_verified',
        'account_deactivated',
        'account_soft_deleted',
        'account_soft_deleted_at',
        'role',
        'services',
    ];

    protected $hidden = [
        'password',
        'otp',
    ];

    protected $casts = [
        'languages' => 'array',
        'specialties' => 'array',
        'services' => 'array',
        'email_verified' => 'boolean',
        'is_verified' => 'boolean',
        'account_deactivated' => 'boolean',
        'account_soft_deleted' => 'boolean',
        'account_soft_deleted_at' => 'datetime',
    ];

    public function businessProfile()
    {
        return $this->belongsTo(Business::class, 'business_profile_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'venue_id');
    }
}
