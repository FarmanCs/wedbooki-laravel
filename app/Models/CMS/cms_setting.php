<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class cms_setting extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'is_in_maintenance_mode',
        'privacy_policy',
        'privacy_policy_updated_at',
        'terms_of_service',
        'terms_of_service_updated_at',
        'refund_policy',
        'refund_policy_updated_at',
    ];

    protected $casts = [
        'is_in_maintenance_mode' => 'boolean',
        'privacy_policy_updated_at' => 'datetime',
        'terms_of_service_updated_at' => 'datetime',
        'refund_policy_updated_at' => 'datetime',
    ];
}
