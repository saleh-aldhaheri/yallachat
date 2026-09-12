<?php

namespace App\Enums;

enum ScheduledMessageStatusEnum: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
    case OVERDUE = 'overdue';
    case PAUSED = 'paused';

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Message has not been sent yet and is waiting for processing.',
            self::SENT => 'Message was sent successfully to all recipients.',
            self::FAILED => 'Message failed to send due to an error, it can be sent in next cycle if exist and can be retired.',
            self::OVERDUE => 'Scheduled time has passed without an attempt to send the message, it can be sent in next cycle if exist and you can send it now.',
            self::PAUSED => 'Schedule is paused and will not send messages until it is resumed.',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-warning/10',
            self::SENT => 'bg-them/10',
            self::FAILED => 'bg-danger/10',
            self::OVERDUE => 'bg-ink-muted/10',
            self::PAUSED => 'bg-you/10',
        };
    }

    public function fontColor(): string
    {
        return match ($this) {
            self::PENDING => 'text-warning',
            self::SENT => 'text-them',
            self::FAILED => 'text-danger',
            self::OVERDUE => 'text-ink-muted',
            self::PAUSED => 'text-you',
        };
    }
}
