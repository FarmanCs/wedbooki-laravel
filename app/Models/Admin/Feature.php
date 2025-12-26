<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feature extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'features';

    protected $fillable = [
        'name',
        'key',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The packages that belong to the feature.
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminPackage::class,
            'feature_package',
            'feature_id',
            'admin_package_id'
        )->withTimestamps();
    }

    /**
     * Scope a query to only include active features.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
