<?php

namespace App\Models\Admin;

use App\Models\Vendor\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'credit_plans';

    protected $fillable = [
        'image',
        'name',
        'description',
        'price',
        'discounted_percentage',
        'no_of_credits',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discounted_percentage' => 'integer',
        'no_of_credits' => 'integer',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

}
