<?php

namespace App\Action;

use App\Ai\Agents\ChatAgent;
use App\Events\MessageSent;
use App\Models\AiModel;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class SendMessageAiAgentAction
{
    public function execute(User $owner,
        User $receiver,
        Chat $chat,
        AiModel $ownerAiModel,
        Collection $incomingMessage): void
    {
        if (! $this->checkCoolDownWindow($chat, 5)) {
            return;
        }

        $agent = new ChatAgent($owner, $ownerAiModel, $chat);

        $response = null;

        if (! $this->checkContextWindow($agent)) {
            $response = "Thanks for contacting {$owner->name}, will reply as soon as possible.";
        } else {

            $messages = json_encode($incomingMessage);

            $promptPayload = <<<PROMPT
            INCOMING MESSAGE LATEST MESSAGES:
            - Representative Owner: {$owner->name} ({$owner->email})
            - Sender: {$receiver->name} ({$receiver->email})
            - Incoming Messages/latest Messages: "{$messages}"
            Compose the auto-reply response according to your system instructions now.
            PROMPT;

            try {
              $response = $agent->prompt($promptPayload, model: $ownerAiModel->name->value)->text;
            }catch (\Throwable){
                $response =  "We’re currently unable to reach the user’s information. Please try again later, or use another method of communication to contact the user.";
            }

        }

        if ($response === null) {
            return;
        }

        $message = Message::create([
            'sender_id' => $owner->id,
            'chat_id' => $chat->id,
            'message' => $response,
        ]);

        broadcast(new MessageSent($receiver->id, $message));

        $this->updateContext($agent, $incomingMessage, $receiver, $response);
    }

    /**
     *  update the model context
     */
    private function updateContext(
        ChatAgent $agent,
        Collection $receivedMessages,
        User $receiver,
        string $responseMessage,
    ): void {
        $messages = [];

        foreach ($receivedMessages as $message) {
            $messages[] = [
                'role' => 'user',
                'user_id' => $message->sender_id,
                'name' => $receiver->name,
                'text' => $message->message,
                'type' => 'received',
                'created_at' => $message->created_at->toISOString(),
            ];
        }

        $messages[] = [
            'role' => 'assistant',
            'user_id' => null,
            'name' => 'AI Agent',
            'text' => $responseMessage,
            'type' => 'sent',
            'created_at' => now()->toISOString(),
        ];

        $agent->setContext($messages);
    }

    /**
     * Returns false when the cooldown is already active, true when a fresh
     * cooldown window was just started.
     */
    private function checkCoolDownWindow(Chat $chat, int $cooldownSec = 10): bool
    {
        return Cache::add("Cooldown-window-chat:{$chat->id}", '', $cooldownSec);
    }

    /**
     * If the context has exceeded its word limit before the window expired, return false.
     */
    private function checkContextWindow(ChatAgent $agent, int $contextLimitWords = 1000): bool
    {
        $context = $agent->getContextSummary();

        if (empty($context)) {
            return true;
        }

        return ! ($context['total_words'] > $contextLimitWords && $context['expires_at'] > Carbon::now()->timestamp);
    }
}
