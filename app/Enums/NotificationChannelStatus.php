<?php

namespace App\Enums;

enum NotificationChannelStatus: string
{
    case CONNECTED = 'connected';
    case DISCONNECTED = 'disconnected';
    case NOT_CONFIGURED = 'not_configured';

    public function label(): string
    {
        return match ($this) {
            self::CONNECTED => 'Connected',
            self::DISCONNECTED => 'Disconnected',
            self::NOT_CONFIGURED => 'Not Configured',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CONNECTED => 'Channel is fully configured and actively sending notifications.',
            self::DISCONNECTED => 'Channel credentials were set up previously, but the connection is currently broken or turned off.',
            self::NOT_CONFIGURED => 'Channel has not been set up or configured yet.',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NOT_CONFIGURED => 'bg-ink-muted/10',
            self::CONNECTED => 'bg-them/10',
            self::DISCONNECTED => 'bg-danger/10',
        };
    }
}
