<?php

namespace App\Action;

use App\Enums\NotificationChannels;
use App\Enums\NotificationChannelStatus;
use App\Integration\Whatsapp\Whatsapp;
use App\Models\NotificationChannel;
use App\Models\User;

class SendWhatsappNotification
{
    /**
     * @throws \Exception
     */
    public function execute(NotificationChannel $channel, User $sender, User $receiver, string $message): void
    {
        if ($channel->type !== NotificationChannels::WHATSAPP) {
            throw new \Exception("Invalid channel WhatsApp Channel expected {$channel->type->value} provided");
        }

        if (! $channel->is_active || $channel->status != NotificationChannelStatus::CONNECTED) {
            throw new \Exception("WhatsApp not connected or not active of {$receiver->name}");
        }

        $message = <<<TEXT
        YallaChat: This is urgent notification message.
        From:  $sender->name
        To: $receiver->name
        Message: $message
        TEXT;

        $whatsapp = app(Whatsapp::class);
        $phoneNumber = data_get($channel->configuration, 'phone_number');

        if (! isset($phoneNumber)) {
            throw new \Exception("phone number not found for {$receiver->name}");
        }

        $phoneNumber = $phoneNumber.'@c.us';

        $whatsapp->sendText(config('services.waha.session'), $phoneNumber, $message);
    }
}
