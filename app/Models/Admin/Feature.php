<?php

namespace App\Models\Admin;

use App\Models\Vendor\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feature extends Model
{
    use HasFactory, SoftDeletes;

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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Fix: Use belongsToMany for proper many-to-many relationship
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(AdminPackage::class, 'feature_package')
            ->withTimestamps();
    }
}
