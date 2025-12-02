<?php

namespace App\Models\Vendor;

use App\Models\Host\Favorite;
use App\Models\Host\Review;
use App\Models\Services\ExtraService;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'business_desc',
        'category_id',
        'subcategory_id',
        'venue_type',
        'member_type',
        'business_registration',
        'business_license_number',
        'rating',
        'is_featured',
        'business_type',
        'website',
        'social_links',
        'postal_code',
        'businessEmail',
        'businessPhone',
        'features',
        'profile_verification',
        'services',
        'faqs',
        'portfolio_images',
        'videos',
        'street_address',
        'capacity',
        'view_count',
        'social_count',
        'last_login',
        'payment_days_advance',
        'payment_days_final',
        'services_radius',
        'advance_percentage',
    ];

    protected $casts = [
        'social_links' => 'array',
        'features' => 'array',
        'services' => 'array',
        'faqs' => 'array',
        'portfolio_images' => 'array',
        'videos' => 'array',
        'last_login' => 'datetime',
    ];


    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // Relationships

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function packages()
    {
        return $this->hasMany(Package::class, 'business_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'business_id');
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'business_id');
    }

    public function timings()
    {
        return $this->hasOne(Timing::class, 'business_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'business_id');
    }
    public function favourites()
    {
        return $this->hasMany(Favorite::class, 'business_id');
    }

    public function extraServices()
    {
        return $this->hasMany(ExtraService::class);
    }
}
