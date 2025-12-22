<?php

namespace App\Models\Admin;

use App\Models\Vendor\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feature extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name',
        'key',
        'silver',
        'gold',
        'platinum',
        'category_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /* =======================
     | Relationships
     ======================= */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class);
    }
}
