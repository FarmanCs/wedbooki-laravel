<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportQuery extends Model
{
    use  HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'subject',
        'priority',
        'message',
        'attachments',
        'status',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }
}
