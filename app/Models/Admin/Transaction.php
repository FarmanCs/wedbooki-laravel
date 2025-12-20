<?php

namespace App\Models\Admin;

use App\Models\Host\Host;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Vendor\Booking;

class Transaction extends Model
{
    use hasfactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'host_id',
        'vendor_id',
        'amount',
        'status',
        'payment_method',
        'payment_reference',
        'sender_id',
        'receiver_id',
        'sender_type',
        'receiver_type',
        'sender_name',
        'receiver_name',
        'redirect_url',
        'acquirer_ref',
        'profile_id',
        'tran_type',
        'tran_class',
        'cart_id',
        'cart_currency',
        'comments',
        'request_body',
        'click_pay_response',
        'click_pay_callback',
        'paid_at',
    ];

    protected $casts = [
        'request_body' => 'array',
        'click_pay_response' => 'array',
        'click_pay_callback' => 'array',
        'paid_at' => 'datetime',
    ];

    /* =========================
     | Relationships
     |=========================*/

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class, 'host_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Polymorphic sender (User, Vendor, Host, etc.)
     */
    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Polymorphic receiver (User, Vendor, Host, etc.)
     */
    public function receiver(): MorphTo
    {
        return $this->morphTo();
    }
}
