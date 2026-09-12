<?php

namespace App\Services;

use App\Enums\NotificationChannels;
use App\Enums\NotificationChannelStatus;
use App\Mail\VerificationCode;
use App\Models\NotificationChannel;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class EmailChannelConfigurationService
{
    private const int MAX_SEND_ATTEMPTS = 5;

    private const int COOLDOWN_SECONDS = 60 * 5;

    private const int CODE_EXPIRATION_IN_SECONDS = 60 * 60;

    public function sendCode(User $user, string $address): bool
    {
        return (bool) RateLimiter::attempt(
            $this->sendKey($user),
            self::MAX_SEND_ATTEMPTS,
            function () use ($address) {
                $code = (string) random_int(100000, 999999);

                Cache::put($this->cacheKey($address), $code, self::CODE_EXPIRATION_IN_SECONDS);

                Mail::to($address)->send(new VerificationCode($code));

                return true;
            },
            self::COOLDOWN_SECONDS
        );
    }

    public function verify(string $address, string $code): bool
    {
        $expected = Cache::get($this->cacheKey($address));

        if (! isset($expected) || $code !== $expected) {
            return false;
        }

        Cache::forget($this->cacheKey($address));

        return true;
    }

    public function persist(User $user, string $address): NotificationChannel
    {
        return NotificationChannel::updateOrCreate(
            [
                'user_id' => $user->id,
                'type' => NotificationChannels::EMAIL->value,
            ],
            [
                'status' => NotificationChannelStatus::CONNECTED->value,
                'configuration' => ['address' => $address],
                'is_active' => true,
            ]
        );
    }

    private function cacheKey(string $address): string
    {
        return 'channel-verification:email:'.$address;
    }

    private function sendKey(User $user): string
    {
        return 'mail-configuration:'.$user->id;
    }
}
