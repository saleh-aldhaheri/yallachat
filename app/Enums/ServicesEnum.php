<?php

namespace App\Enums;

enum ServicesEnum: string
{
    case GOOGLE_CALENDAR = 'google_calendar';

    public function providers(): string
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => 'google',
        };
    }

    public function scopes(): string
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => 'calendar',
        };
    }

    /**
     *  Convert from provider scope to the service
     */
    public static function fromProvider(string $provider, string $service): self
    {
        return match ($provider.'_'.$service) {
            'google_calendar' => self::GOOGLE_CALENDAR,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => 'Google Calendar',
        };
    }

    public function image(): string
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => 'https://img.icons8.com/?size=100&id=WKF3bm1munsk&format=png&color=000000',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::GOOGLE_CALENDAR => 'Lets the AI see your schedule and activities so it can reason correctly and give you accurate, situation-aware answers. You can disable this at any time if you don\'t want it connected.',
        };
    }
}
