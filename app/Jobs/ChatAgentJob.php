<?php

namespace App\Jobs;

use App\Action\SendMessageAiAgentAction;
use App\Models\AiModel;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class ChatAgentJob implements ShouldQueue
{
    use Queueable, SerializesModels;
    public function backoff(): array
    {
        return [
            1,
            3,
            5,
        ];
    }

    /**
     * @param  int  $ownerId  the AI owner who the agent will reply on behalf of
     * @param  int  $receiverId  who will receive the AI message
     * @param  Collection  $incomingMessages  messages captured at send time
     */
    public function __construct(
        protected int $ownerId,
        protected int $receiverId,
        protected int $ownerAiModelId,
        protected int $chatId,
        protected Collection $incomingMessages,
    ) {
        $this->onQueue('ai-agent-chat');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $owner = User::findOrFail($this->ownerId);
        $receiver = User::findOrFail($this->receiverId);
        $chat = Chat::findOrFail($this->chatId);
        $ownerAiModel = AiModel::findOrFail($this->ownerAiModelId);

        app(SendMessageAiAgentAction::class)
            ->execute($owner, $receiver, $chat, $ownerAiModel, $this->incomingMessages);
    }
}
