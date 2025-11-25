<?php

namespace App\Models\Host;

use App\Models\Vendor\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Host extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'partner_full_name',
        'partner_email',
        'country',
        'email',
        'linked_email',
        'country_code',
        'phone_no',
        'profile_image',
        'about',
        'wedding_date',
        'password',
        'googleId',
        'appleId',
        'signupMethod',
        'status',
        'role',
        'otp',
        'isVerified',
        'pending_email',
        'category',
        'event_type',
        'estimated_guests',
        'event_budget',
        'interested_vendors',
        'joinDate'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'wedding_date' => 'date',
        'event_budget' => 'decimal:2',
        'isVerified' => 'boolean',
        'password' => 'hashed',
    ];

    public function favouriteBusinesses()
    {
        return $this->belongsToMany(
            Business::class,
            'host_business',
            'host_id',
            'business_id',
            'id',
            'id'
        );
    }
}
