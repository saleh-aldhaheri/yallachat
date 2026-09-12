<?php

namespace App\Action;

use App\Jobs\ChatAgentJob;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;

class TriggerAiToSendAction
{
    public function execute(User $owner, Chat $chat, User $receiver): void
    {
        $lastOwnerMessage = Message::where('chat_id', $chat->id)
            ->where('sender_id', $owner->id)
            ->latest()
            ->first();

        $query = Message::where('chat_id', $chat->id)
            ->where('sender_id', $receiver->id);

        if ($lastOwnerMessage) {
            $query->where('created_at', '>', $lastOwnerMessage->created_at);
        }

        $messages = $query->get();

        $aiModel = $owner->aiModels()->useable()->first();

        if (! $aiModel) {
            return;
        }

        dispatch(new ChatAgentJob(
            $owner->id,
            $receiver->id,
            $aiModel->id,
            $chat->id,
            $messages,
        ));
    }
}
