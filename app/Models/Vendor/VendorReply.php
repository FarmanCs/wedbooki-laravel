<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Host\Review;
use App\Models\Vendor\Business;
use App\Models\Vendor\Vendor;

class VendorReply extends Model
{


    protected $table = 'review_replies';

    protected $fillable = [
        'review_id',
        'business_id',
        'vendor_id',
        'text',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Reply belongs to a Review
    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id');
    }

    // Reply belongs to a Business
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    // Reply belongs to a Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}

