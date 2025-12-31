<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms_settings';

    protected $fillable = [
        'is_in_maintenance_mode',
        'privacy_policy',
        'privacy_policy_updated_at',
        'terms_of_service',
        'terms_of_service_updated_at',
        'refund_policy',
        'refund_policy_updated_at',
        'site_name',
        'site_logo',
        'favicon',
        'site_email',
        'site_phone',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'maintenance_mode',
        'maintenance_message',
        'default_timezone',
        'default_language',
        'currency',
    ];

    protected $casts = [
        'is_in_maintenance_mode' => 'boolean',
        'maintenance_mode' => 'boolean',
        'privacy_policy_updated_at' => 'datetime',
        'terms_of_service_updated_at' => 'datetime',
        'refund_policy_updated_at' => 'datetime',
    ];
}
