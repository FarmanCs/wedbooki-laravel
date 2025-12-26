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

    protected $table = 'admin_packages';

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

    /**
     * Get the category that owns the package.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The features that belong to the package.
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(
            Feature::class,
            'feature_package',
            'admin_package_id',
            'feature_id'
        )->withTimestamps();
    }

    /**
     * Scope a query to only include active packages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include published packages.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
