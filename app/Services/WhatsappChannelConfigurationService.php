<?php

namespace App\Services;

use App\Enums\NotificationChannels;
use App\Enums\NotificationChannelStatus;
use App\Integration\Whatsapp\Whatsapp;
use App\Models\NotificationChannel;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class WhatsappChannelConfigurationService
{
    public function __construct(
        private readonly Whatsapp $whatsapp
    ) {}

    private const int MAX_SEND_ATTEMPTS = 3;

    private const int COOLDOWN_IN_SECONDS = 60 * 5;

    private const int CODE_EXPRESSION_SECONDS = 60 * 60;

    public function send(string $phoneNumber, User $user): bool
    {
        return (bool) RateLimiter::attempt(
            $this->sendKey($user),
            self::MAX_SEND_ATTEMPTS,
            function () use ($phoneNumber, $user) {
                $code = (string) random_int(100000, 999999);

                Cache::put($this->cacheKey($phoneNumber), $code, self::CODE_EXPRESSION_SECONDS);

                $hours = self::CODE_EXPRESSION_SECONDS / 3600;

                $text = "YallaChat:\nHello {$user->name}, your confirmation code is {$code}.\nThis code will expire within {$hours} hour(s).";

                $this->whatsapp->sendText(config('services.waha.session'), $phoneNumber.'@c.us', $text);

                return true;
            },
            self::COOLDOWN_IN_SECONDS
        );
    }

    public function verify(string $phoneNumber, string $code): bool
    {
        $expected = Cache::get($this->cacheKey($phoneNumber));

        if (! isset($expected) || $expected !== $code) {
            return false;
        }

        Cache::forget($this->cacheKey($phoneNumber));

        return true;
    }

    public function persist(User $user, string $phoneNumber): NotificationChannel
    {
        return NotificationChannel::updateOrCreate(
            [
                'user_id' => $user->id,
                'type' => NotificationChannels::WHATSAPP,
            ],
            [
                'configuration' => ['phone_number' => $phoneNumber],
                'is_active' => true,
                'status' => NotificationChannelStatus::CONNECTED,
            ]
        );
    }

    private function cacheKey(string $phoneNumber): string
    {
        return 'channel-verification:whatsapp:'.$phoneNumber;
    }

    private function sendKey(User $user): string
    {
        return 'channel-verification:whatsapp:'.$user->id;
    }
}
