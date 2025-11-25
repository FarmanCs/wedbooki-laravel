<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'host_id',
        'vendor_id',
        'rating',
        'comment',
        'vendor_replies',
    ];

    protected $casts = [
        'vendor_replies' => 'array',
        'rating' => 'integer',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function host()
    {
        return $this->belongsTo(Host::class, 'host_id');
    }

    public function vendorReplies()
    {
        return $this->hasMany(ReviewReply::class, 'review_id');
    }
}
