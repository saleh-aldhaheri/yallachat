<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduledMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic',
        'description',
        'is_active',
        'languages',
        'schedule_type',
        'start_date',
        'frequency',
        'run_at',
        'run_dates',
        'ends_type',
        'ends_on',
        'sent_count',
    ];

    protected $casts = [
        'languages' => 'array',
        'run_dates' => 'array',
        'start_date' => 'date',
        'run_at' => 'string',
        'ends_on' => 'date',
        'sent_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function chatScheduledMessages(): HasMany
    {
        return $this->hasMany(ChatScheduledMessage::class);
    }

    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(
            Chat::class,
            'chat_scheduled_message',
            'scheduled_message_id',
            'chat_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
