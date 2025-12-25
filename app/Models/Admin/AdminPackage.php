<?php

namespace App\Models\Admin;

use App\Models\Vendor\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminPackage extends Model
{
    use HasFactory, SoftDeletes;

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
        'monthly_price' => 'decimal:2',
        'quarterly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Fix: Use belongsToMany for proper many-to-many relationship
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'feature_package')
            ->withTimestamps();
    }
}
