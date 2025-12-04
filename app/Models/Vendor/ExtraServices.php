<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraService extends Model
{
    use HasFactory;

    // Table name (optional if following Laravel naming convention)
    protected $table = 'extra_services';

    // Mass assignable attributes
    protected $fillable = [
        'business_id',
        'service_id',
        'name',
        'price',
    ];

    /**
     * Relationships
     */

    // Optional: Relation to Business
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    // Optional: Relation to Service
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
