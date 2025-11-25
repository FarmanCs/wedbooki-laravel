<?php

namespace App\Models\Vendor;

use App\Models\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'category',
        'subcategory',
        'business_registration',
        'business_license_number',
        'vendor_type',
        'business_desc',
        'features',
        'businessEmail',
        'businessPhone',
        'country',
        'city',
        'zip',
        'street_address',
        'website',
        'social_links',
        'postal_code',
        'capacity',
        'services',
        'addi_services',
        'faqs',
        'PaymentDaysAdvance',
        'PaymentDaysFinal',
        'ServicesRadius',
        'advancePercentage',
        'portfolio_images',
        'videos',
        'packages',
        'view_count',
        'social_count',
    ];

    protected $casts = [
        'features' => 'array',
        'social_links' => 'array',
        'services' => 'array',
        'addi_services' => 'array',
        'faqs' => 'array',
        'portfolio_images' => 'array',
        'videos' => 'array',
        'packages' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category');
    }

    public function packagesRelation()
    {
        return $this->hasMany(Package::class, 'business_id');
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'business_profile_id');
    }

    public function timings()
    {
        return $this->hasOne(Timing::class, 'business_id');
    }
}
