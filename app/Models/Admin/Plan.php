<?php

namespace App\Models\Admin;

use App\Models\Vendor\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name',
        'description',
        'badge',
        'monthly_price',
        'quarterly_price',
        'yearly_price',
        'category_id',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'published_at' => 'datetime',
        'monthly_price' => 'decimal:2',
        'quarterly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
    ];

    /* =======================
     | Relationships
     ======================= */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class);
    }
}
