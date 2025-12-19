<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cms_setting_id',
        'question',
        'answer',
        'is_published',
        'last_updated',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'last_updated' => 'datetime',
    ];

    public function cmsSetting()
    {
        return $this->belongsTo(CMSSetting::class);
    }
}
