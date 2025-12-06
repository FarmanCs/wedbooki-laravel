<?php

namespace App\Models\services;

use App\Models\Vendor\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraService extends Model
{
    // Specify table if Laravel cannot infer it (optional if table is 'extra_services')
    protected $table = 'extra_services';

    // Mass assignable fields
    protected $fillable = [
        'business_id',
        'service_id',
        'name',
        'price',
    ];

    /**
     * Relationship: ExtraService belongs to a Business
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Relationship: ExtraService belongs to a Service
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
