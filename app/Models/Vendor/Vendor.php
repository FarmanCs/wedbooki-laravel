<?php

namespace App\Models\Vendor;

use App\Models\services\Service;
use App\Models\Vendor\Category;
use App\Models\Vendor\Business;
use App\Models\Vendor\Booking;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Vendor extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;


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

    protected $hidden = [
        'password',
        'otp',
    ];

    protected $casts = [
        'languages' => 'array',
        'specialties' => 'array',
        'team_members' => 'integer',
        'email_verified' => 'boolean',
        'account_deactivated' => 'boolean',
        'account_soft_deleted' => 'boolean',
        'account_soft_deleted_at' => 'datetime',
        'last_login' => 'datetime',
    ];

    // -------------------------
    //      RELATIONSHIPS
    // -------------------------

    /**
     * Vendor belongs to a business
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    /**
     * Vendor belongs to a category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Vendor has many bookings (vendor_id)
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'vendor_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'vendor_id');
    }
}
