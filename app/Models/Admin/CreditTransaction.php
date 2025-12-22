<?php

namespace App\Models\Admin;

use App\Models\Vendor\Business;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'credits_transactions';

    protected $fillable = [
        'business_id',
        'no_of_credits',
        'amount',
        'from',
        'to',
        'tran_type',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'no_of_credits' => 'integer',
        'amount'        => 'decimal:2',
        'tran_type'     => 'string',
        'deleted_at'    => 'datetime',
    ];


    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT  = 'debit';

    //Relationship: CreditTransaction belongs to a Business

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    /**
     * Scope: Only credit transactions
     */
    public function scopeCredits($query)
    {
        return $query->where('tran_type', self::TYPE_CREDIT);
    }


    public function scopeDebits($query)
    {
        return $query->where('tran_type', self::TYPE_DEBIT);
    }
}
