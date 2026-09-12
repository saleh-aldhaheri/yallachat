<?php

namespace App\Action;

use App\Enums\NotificationChannels;
use App\Enums\NotificationChannelStatus;
use App\Mail\EmailNotification;
use App\Models\NotificationChannel;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification
{
    /**
     * @throws \Exception
     */
    public function execute(NotificationChannel $channel, User $sender, User $receiver, string $message): void
    {
        if($channel->type !== NotificationChannels::EMAIL) {
            throw new \Exception("Invalid channel Email Channel expected {$channel->type->value} provided");
        }

        if(!$channel->is_active || $channel->status != NotificationChannelStatus::CONNECTED) {
            throw new \Exception("Email not connected or not active of {$receiver->name}");
        }

        $address = data_get($channel->configuration, 'address');

        if(!isset($address)) {
            throw new \Exception("Invalid configuration address");
        }

        Mail::to($address)->send(new EmailNotification($sender->name, $receiver->name, $message));
    }
}
