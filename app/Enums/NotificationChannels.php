<?php

namespace App\Enums;

enum NotificationChannels: string
{
    case EMAIL = 'email';
    case WHATSAPP = 'whatsapp';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'Email',
            self::WHATSAPP => 'WhatsApp',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::EMAIL => 'Receive system alerts and notifications directly via Email.',
            self::WHATSAPP => 'Receive system alerts and notifications directly via WhatsApp.',
        };
    }

    public function image(): string
    {
        return match ($this) {
            self::EMAIL => 'https://img.icons8.com/?size=100&id=X0mEIh0RyDdL&format=png&color=000000',
            self::WHATSAPP => 'https://img.icons8.com/?size=100&id=a8unpNrefMCC&format=png&color=000000',
        };
    }
}
