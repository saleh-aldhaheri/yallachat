<?php

namespace App\Models;

use App\Enums\NotificationChannels;
use App\Enums\NotificationChannelStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationChannel extends Model
{
    protected $fillable = [
        'type',
        'user_id',
        'description',
        'is_active',
        'status',
        'configuration',
    ];

    protected $casts = [
        'type' => NotificationChannels::class,
        'status' => NotificationChannelStatus::class,
        'configuration' => 'array',
    ];

    public function scopeUseable($query)
    {
        return $query->where('is_active', true)->where('status', NotificationChannelStatus::CONNECTED);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
