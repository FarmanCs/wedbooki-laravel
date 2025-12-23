<?php

namespace App\Models\Vendor;

use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'type',
        'description',
        'image',
    ];

    protected $appends = ['image_url'];


    // If each category has many subcategories
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }

    // If each category has many businesses
    public function businesses()
    {
        return $this->hasMany(Business::class, 'category_id');
    }


    // Accessor: Get full S3 URL when accessing $category->image_url
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::disk('s3')->url($this->image) : null;
    }

    // Mutator: Automatically convert to full URL when saving
    // WARNING: Only use this if you want to store full URLs in database
    public function setImageAttribute($value)
    {
        $this->attributes['image'] = $value ? Storage::disk('s3')->url($value) : null;
    }
}
