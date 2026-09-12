<?php

namespace App\Action;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;

class SendMessageAction
{
    public function execute(Chat $chat, string $message): Message
    {
        $newMessage = Message::create([
            'sender_id' => auth()->id(),
            'chat_id' => $chat->id,
            'message' => $message,
        ]);

        $participants = $chat->participants()->whereNot('participant_id', auth()->id())->get();

        $participants->each(function ($participant) use ($newMessage) {
            broadcast(new MessageSent($participant->id, $newMessage));
        });

        return $newMessage;
    }
}
