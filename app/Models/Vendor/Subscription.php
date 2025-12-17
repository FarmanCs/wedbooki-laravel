<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table name (optional if follows convention)
     */
    protected $table = 'subscriptions';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'description',
        'badge',
        'monthly_price',
        'yearly_price',
        'package_type',
        'category_id',
        'is_active',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price'  => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    /* =====================================================
     | RELATIONSHIPS
     |=====================================================*/

    /**
     * Subscription belongs to a category
     */
    public function category()
    {
        return $this->belongsTo(
            Category::class,
            'category_id'
        );
    }

    /**
     * Subscription belongs to many vendors
     */
    public function vendors()
    {
        return $this->belongsToMany(
            Vendor::class,
            'subscription_vendor'
        )->withPivot([
            'starts_at',
            'ends_at',
            'status',
        ])->withTimestamps();
    }

    /**
     * Subscription has many payments
     */
    public function payments()
    {
        return $this->hasMany(
            \App\Models\SubscriptionPayment::class,
            'subscription_id'
        );
    }

    /**
     * Subscription has many features
     */
    public function features()
    {
        return $this->hasMany(
            \App\Models\SubscriptionFeature::class,
            'subscription_id'
        );
    }

    /* =====================================================
     | SCOPES
     |=====================================================*/

    /**
     * Only active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Monthly subscriptions
     */
    public function scopeMonthly($query)
    {
        return $query->where('package_type', 'monthly');
    }

    /**
     * Yearly subscriptions
     */
    public function scopeYearly($query)
    {
        return $query->where('package_type', 'yearly');
    }
}
