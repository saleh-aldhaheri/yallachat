<?php

namespace App\Services;

use App\Enums\NotificationChannels;
use App\Jobs\EmailNotificationJob;
use App\Jobs\WhatsappNotificationJob;
use App\Models\NotificationChannel;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

class SendNotificationService
{
    public const int MAX_ATTEMPTS = 3;

    public const int COOLDOWN_SECONDS = 60 * 60;

    public function cooldownForHumans(): string
    {
        $seconds = self::COOLDOWN_SECONDS;

        if ($seconds >= 3600 && $seconds % 3600 === 0) {
            $hours = $seconds / 3600;

            return $hours === 1 ? '1 hour' : "{$hours} hours";
        }

        if ($seconds >= 60 && $seconds % 60 === 0) {
            $minutes = $seconds / 60;

            return $minutes === 1 ? '1 minute' : "{$minutes} minutes";
        }

        return $seconds === 1 ? '1 second' : "{$seconds} seconds";
    }

    public function send(User $from, User $to, string $message): bool
    {
        $executed = RateLimiter::attempt(
            $this->key($from),
            self::MAX_ATTEMPTS,
            function () use ($from, $to, $message) {
                $to->notificationChannels()
                    ->useable()
                    ->get()
                    ->each(function (NotificationChannel $notificationChannel) use ($from, $to, $message) {
                        if ($notificationChannel->type == NotificationChannels::EMAIL) {
                            EmailNotificationJob::dispatch($notificationChannel->id, $from->id, $to->id, $message);
                        } elseif ($notificationChannel->type == NotificationChannels::WHATSAPP) {
                            WhatsappNotificationJob::dispatch($notificationChannel->id, $from->id, $to->id, $message);
                        }
                    });

                return true;
            },
            self::COOLDOWN_SECONDS
        );

        return (bool) $executed;
    }

    private function key(User $from): string
    {
        return 'send-urgent-notification:'.$from->id;
    }
}
