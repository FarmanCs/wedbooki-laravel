<?php

namespace App\Models\Vendor;

use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'type',
        'description',
        'image',
    ];

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
}
