<?php

namespace App\Models\Host;

use App\Models\Vendor;
use App\Models\Vendor\Business;
use App\Models\Vendor\VendorReply;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'business_id',
        'points',
        'text',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // A review belongs to a host
    public function host()
    {
        return $this->belongsTo(Host::class, 'host_id');
    }

    // A review belongs to a business/vendor
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function vendorReplies()
    {
        return $this->hasMany(VendorReply::class, 'review_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
