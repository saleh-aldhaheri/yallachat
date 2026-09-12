<?php

namespace App\Jobs;

use App\Action\SendScheduledMessageAction;
use App\Models\ChatScheduledMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendScheduledMessagesJob implements ShouldQueue
{
    use Queueable;

    public function backoff(): array
    {
        return [
            30,
            60,
            120,
        ];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(public int $id)
    {
        $this->onQueue('ai-scheduled-messages');
    }

    /**
     * Execute the job.
     *
     * @throws \Throwable
     */
    public function handle(): void
    {

        $chatScheduledMessage = ChatScheduledMessage::find($this->id);

        if (! $chatScheduledMessage) {
            throw new \Exception('unable to find the scheduled message');
        }

        app(SendScheduledMessageAction::class)->execute($chatScheduledMessage);
    }
}
