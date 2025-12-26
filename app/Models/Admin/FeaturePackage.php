<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class FeaturePackage extends Model
{
    protected $fillable = [
        'admin_package_id',
        'feature_id',
    ];

    /**
     * Get the package that owns this pivot.
     */
    public function package()
    {
        return $this->belongsTo(AdminPackage::class, 'admin_package_id');
    }

    /**
     * Get the feature that this pivot refers to.
     */
    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }
}

