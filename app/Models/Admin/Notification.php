<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'message',
        'type',
        'recipients',
        'delivery_method',
        'send_mode',
        'scheduled_at',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    // Define constants for enums
    public const TYPES = ['Announcement', 'Alert', 'Reminder'];
    public const RECIPIENTS = ['Users', 'Vendors', 'All'];
    public const DELIVERY_METHODS = ['Email', 'SMS', 'Push Notification', 'All'];
    public const SEND_MODES = ['Send Immediately', 'Schedule', 'Save as draft'];
    public const STATUS = ['draft', 'published'];
}
