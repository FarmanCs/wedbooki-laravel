<?php

namespace App\Models\Admin;

use App\Models\Host\Host;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'host_id',
        'business_id',
        'vendor_id',
        'sender_name',
        'receiver_name',
        'invoice_number',
        'payment_type',
        'total_amount',
        'advance_amount',
        'remaining_amount',
        'base_amount_paid',
        'platform_fee_from_user',
        'total_user_paid',
        'vendor_share',
        'platform_share',
        'commission_rate',
        'vendor_plan_name',
        'advance_paid_date',
        'final_paid_date',
        'is_advance_paid',
        'is_final_paid',
        'advance_due_date',
        'final_due_date',
        'advance_percentage',
        'full_payment_only',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'base_amount_paid' => 'decimal:2',
        'platform_fee_from_user' => 'decimal:2',
        'total_user_paid' => 'decimal:2',
        'vendor_share' => 'decimal:2',
        'platform_share' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'advance_paid_date' => 'datetime',
        'final_paid_date' => 'datetime',
        'is_advance_paid' => 'boolean',
        'is_final_paid' => 'boolean',
        'advance_due_date' => 'datetime',
        'final_due_date' => 'datetime',
        'advance_percentage' => 'integer',
        'full_payment_only' => 'boolean',
    ];

    /* =========================
     | Constants
     |=========================*/

    public const PAYMENT_TYPE_ADVANCE = 'advance';
    public const PAYMENT_TYPE_FINAL = 'final';
    public const PAYMENT_TYPE_FULL = 'full';

    public const PAYMENT_TYPES = [
        self::PAYMENT_TYPE_ADVANCE,
        self::PAYMENT_TYPE_FINAL,
        self::PAYMENT_TYPE_FULL,
    ];

    /* =========================
     | Relationships
     |=========================*/

    /**
     * Get the booking that owns the invoice.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the host that owns the invoice.
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }

    /**
     * Get the business that owns the invoice.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the vendor that owns the invoice.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /* =========================
     | Scopes
     |=========================*/

    /**
     * Scope a query to only include paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('is_final_paid', true);
    }

    /**
     * Scope a query to only include unpaid invoices.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_final_paid', false);
    }

    /**
     * Scope a query to only include advance paid invoices.
     */
    public function scopeAdvancePaid($query)
    {
        return $query->where('is_advance_paid', true);
    }

    /**
     * Scope a query to only include advance unpaid invoices.
     */
    public function scopeAdvanceUnpaid($query)
    {
        return $query->where('is_advance_paid', false);
    }

    /**
     * Scope a query to only include overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('is_final_paid', false)
            ->where('final_due_date', '<', now());
    }

    /**
     * Scope a query to filter by payment type.
     */
    public function scopeByPaymentType($query, string $type)
    {
        return $query->where('payment_type', $type);
    }

    /**
     * Scope a query to only include full payment invoices.
     */
    public function scopeFullPaymentOnly($query)
    {
        return $query->where('full_payment_only', true);
    }

    /* =========================
     | Accessors & Mutators
     |=========================*/

    /**
     * Check if the invoice is fully paid.
     */
    public function isFullyPaid(): bool
    {
        return $this->is_final_paid;
    }

    /**
     * Check if the invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return !$this->is_final_paid && $this->final_due_date && $this->final_due_date->isPast();
    }

    /**
     * Check if the advance payment is overdue.
     */
    public function isAdvanceOverdue(): bool
    {
        return !$this->is_advance_paid && $this->advance_due_date && $this->advance_due_date->isPast();
    }

    /**
     * Get the payment status.
     */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->is_final_paid) {
            return 'Paid';
        }

        if ($this->is_advance_paid && !$this->is_final_paid) {
            return 'Partially Paid';
        }

        if ($this->isOverdue()) {
            return 'Overdue';
        }

        return 'Unpaid';
    }

    /* =========================
     | Helper Methods
     |=========================*/

    /**
     * Generate a unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $lastInvoice = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    /**
     * Calculate amounts based on total and commission.
     */
    public function calculateAmounts(): void
    {
        if ($this->total_amount && $this->commission_rate) {
            $this->platform_share = ($this->total_amount * $this->commission_rate) / 100;
            $this->vendor_share = $this->total_amount - $this->platform_share;
        }

        if ($this->full_payment_only) {
            $this->advance_amount = 0;
            $this->remaining_amount = $this->total_amount;
        } else {
            $this->advance_amount = ($this->total_amount * $this->advance_percentage) / 100;
            $this->remaining_amount = $this->total_amount - $this->advance_amount;
        }
    }

    /**
     * Mark advance as paid.
     */
    public function markAdvanceAsPaid(): void
    {
        $this->is_advance_paid = true;
        $this->advance_paid_date = now();
        $this->save();
    }

    /**
     * Mark final payment as paid.
     */
    public function markFinalAsPaid(): void
    {
        $this->is_final_paid = true;
        $this->final_paid_date = now();
        $this->save();
    }
}
