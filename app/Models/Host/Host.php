<?php

namespace App\Models\Host;

use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Host extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $guard = 'web';
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
        'google_id',
        'apple_id',
        'signup_method',
        'status',
        'role',
        'account_deactivated',
        'account_soft_deleted',
        'account_soft_deleted_at',
        'otp',
        'is_verified',
        'pending_email',
        'category',
        'event_type',
        'estimated_guests',
        'event_budget',
        'interested_vendors',
        'join_date',
        'otp_expires_at'
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
        'is_verified' => 'boolean',
        'password' => 'hashed',
        'category' => 'string',
        'otp' => 'integer',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'host_id');
    }
    public function favourites()
    {
        return $this->hasMany(Favourites::class);
    }

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

    public function guestGroups()
    {
        return $this->hasMany(GuestGroup::class, 'host_id');
    }


    public function personalizedChecklists()
    {
        return $this->hasMany(HostPersonalizedChecklist::class, 'host_id');
    }

    public function checklists()
    {
        return $this->hasMany(Checklist::class, 'host_id', 'id');
    }

    public function initials(): string
    {
        return Str::of($this->full_name)
        ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }


}
