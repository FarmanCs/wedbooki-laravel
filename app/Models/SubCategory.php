<?php

namespace App\Models;

use App\Models\Vendor\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    // Table name (optional if it follows Laravel naming convention)
    protected $table = 'sub_categories';

    // Mass assignable fields
    protected $fillable = [
        'type',
        'category_id',
        'description',
        'image',
    ];

    /**
     * Relationship: SubCategory belongs to a Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Example: If a subcategory has many items (optional)
     * public function items()
     * {
     *      return $this->hasMany(Item::class);
     * }
     */
}
