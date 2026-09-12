<?php

namespace App\Jobs;

use App\Action\SendWhatsappNotification;
use App\Models\NotificationChannel;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class WhatsappNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private int             $channelId,
        private readonly int    $senderId,
        private readonly int    $receiverId,
        private readonly string $message,
    )
    {
        $this->onQueue('priority-notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $channel = NotificationChannel::find($this->channelId);
        $sender = User::find($this->senderId);
        $receiver = User::find($this->receiverId);

        if(!$channel || !$sender || !$receiver) {
            return;
        }
        (new SendWhatsappNotification())->execute($channel, $sender, $receiver, $this->message);
    }
}
