<?php

namespace App\Models;

use App\Enums\ScheduledMessageStatusEnum;
use Illuminate\Database\Eloquent\Model;

class ChatScheduledMessage extends Model
{
    protected $table = 'chat_scheduled_message';

    protected $fillable = [
        'scheduled_message_id',
        'chat_id',
        'message',
        'note',
        'status',
        'is_paused',
        'last_attempt_at',
        'sent_at',
    ];

    protected $casts = [
        'status' => ScheduledMessageStatusEnum::class,
        'last_attempt_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function scheduledMessage()
    {
        return $this->belongsTo(ScheduledMessage::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
