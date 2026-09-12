<?php

namespace App\Events;

use App\Models\ChatScheduledMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScheduledMessageStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public int $senderId, public ChatScheduledMessage $chatScheduledMessage) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('scheduled_message.'.$this->senderId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->chatScheduledMessage->id,
            'status' => $this->chatScheduledMessage->status->value,
        ];
    }
}
