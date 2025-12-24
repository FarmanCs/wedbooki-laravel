<?php

namespace App\Models\Admin;

use App\Models\Vendor\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminPackage extends Model
{
    use  SoftDeletes;

    protected $fillable = [
        'name',
        // Silver
        'silver_description',
        'silver_badge',
        'silver_monthly_price',
        'silver_quarterly_price',
        'silver_yearly_price',
        // Gold
        'gold_description',
        'gold_badge',
        'gold_monthly_price',
        'gold_quarterly_price',
        'gold_yearly_price',
        // Platinum
        'platinum_description',
        'platinum_badge',
        'platinum_monthly_price',
        'platinum_quarterly_price',
        'platinum_yearly_price',
        // Common
        'category_id',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'silver_monthly_price' => 'decimal:2',
        'silver_quarterly_price' => 'decimal:2',
        'silver_yearly_price' => 'decimal:2',
        'gold_monthly_price' => 'decimal:2',
        'gold_quarterly_price' => 'decimal:2',
        'gold_yearly_price' => 'decimal:2',
        'platinum_monthly_price' => 'decimal:2',
        'platinum_quarterly_price' => 'decimal:2',
        'platinum_yearly_price' => 'decimal:2',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Helper method to get tier data
    public function getTier(string $tier): array
    {
        $lowerTier = strtolower($tier);

        return [
            'name' => ucfirst($tier),
            'description' => $this->{"{$lowerTier}_description"},
            'badge' => $this->{"{$lowerTier}_badge"},
            'monthly_price' => $this->{"{$lowerTier}_monthly_price"},
            'quarterly_price' => $this->{"{$lowerTier}_quarterly_price"},
            'yearly_price' => $this->{"{$lowerTier}_yearly_price"},
        ];
    }

    // Get all tiers
    public function getAllTiers(): array
    {
        return [
            'silver' => $this->getTier('silver'),
            'gold' => $this->getTier('gold'),
            'platinum' => $this->getTier('platinum'),
        ];
    }
}
