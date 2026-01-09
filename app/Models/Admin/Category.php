<?php

namespace App\Models\Admin;

use App\Models\SubCategory;
use App\Models\Vendor\Business;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use SoftDeletes, HasFactory ;

    protected $table = 'categories';

    protected $fillable = [
        'type',
        'description',
        'image',
    ];


    //when we add virtual attributes or column to form existing colum names
//    protected $appends = ['image_url'];
//
//    public function ImageUrl(): Attribute
//    {
//        return Attribute::make(
//            get: function () {return 'asdf';},
//        );
//    }


    // If each category has many subcategories
    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'category_id');
    }

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
