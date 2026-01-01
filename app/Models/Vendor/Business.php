<?php

namespace App\Models\Vendor;

use App\Models\Admin\CreditTransaction;
use App\Models\Host\Favorite;
use App\Models\Host\Review;
use App\Models\Admin\Category;
use App\Models\Services\ExtraService;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'business_desc',
        'category_id',
        'subcategory_id',
        'vendor_id',
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
        'business_email',
        'business_phone',
        'features',
        'profile_verification',
        'services',
        'faqs',
        'portfolio_images',
        'videos',
        'street_address',
        'city',
        'country',
        'capacity',
        'view_count',
        'social_count',
        'last_login',
        'payment_days_advance',
        'payment_days_final',
        'services_radius',
        'advance_percentage',
        'profile_image',
        'cover_image',
        'chat_image',
        'chat_video',
        'chat_document',
    ];

    protected $casts = [
        'social_links' => 'array',
        'features' => 'array',
        'services' => 'array',
        'faqs' => 'array',
        'portfolio_images' => 'array',
        'videos' => 'array',
        'is_featured' => 'boolean',
        'rating' => 'float',
        'payment_days_advance' => 'integer',
        'payment_days_final' => 'integer',
        'services_radius' => 'integer',
        'advance_percentage' => 'float',
        'last_login' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class, 'business_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'business_id');
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
        return $this->hasMany(ExtraService::class, 'business_id');
    }

    public function creditTransactions()
    {
        return $this->hasMany(CreditTransaction::class, 'business_id');
    }
}
