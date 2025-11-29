<?php

namespace App\Models\services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Vendor;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'name',
        'description',
        'price',
        'img',
        'vendor_id',
        'category',
    ];

    protected $casts = [
        'img' => 'array', // cast JSON to array
    ];

    //Service belongs to a Vendor
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    //Service has many ExtraServices

    public function extraServices(): HasMany
    {
        return $this->hasMany(ExtraService::class, 'service_id');
    }
}
