<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}.{receiverId}', function ($user, $chatId, $receiverId) {
    return (int) $user->id === (int) $receiverId;
});

Broadcast::channel('scheduled_message.{senderId}', function ($user, $senderId) {
    return (int) $user->id === (int) $senderId;
});

Broadcast::channel('online', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
    ];
});
