<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendors';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone_no',
        'pending_email',
        'country_code',
        'profile_image',
        'years_of_experience',
        'languages',
        'team_members',
        'specialties',
        'about',
        'country',
        'city',
        'role',
        'password',
        'category_id',
        'postal_code',
        'otp',
        'business_id',
        'profile_verification',
        'email_verified',
        'stripe_account_id',
        'bank_last4',
        'bank_name',
        'account_holder_name',
        'payout_currency',
        'custom_vendor_id',
        'google_id',
        'signup_method',
        'cover_image',
        'last_login',
        'account_deactivated',
        'account_soft_deleted',
        'account_soft_deleted_at',
        'auto_hard_delete_after_days',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'languages' => 'array',
        'specialties' => 'array',
        'email_verified' => 'boolean',
        'account_deactivated' => 'boolean',
        'account_soft_deleted' => 'boolean',
        'last_login' => 'datetime',
        'account_soft_deleted_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    // Vendor belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Vendor belongs to a business
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    // Example: Vendor has many reviews (if you have a VendorReview model)
    public function reviews()
    {
        return $this->hasMany(VendorReview::class);
    }

    // Example: Vendor has many bookings (if you have a Booking model)
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Mutators
     */

    // Hash password automatically when setting it
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Accessors
     */

    // Generate a full display name (example)
    public function getDisplayNameAttribute()
    {
        return $this->full_name ?: $this->email;
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Generate custom_vendor_id automatically if not provided
        static::creating(function ($vendor) {
            if (!$vendor->custom_vendor_id) {
                $vendor->custom_vendor_id = 'VND-' . Str::upper(Str::random(8));
            }
        });
    }
}
