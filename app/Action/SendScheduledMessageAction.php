<?php

namespace App\Action;

use App\Ai\Agents\ScheduledMessageAgent;
use App\Enums\ScheduledMessageStatusEnum;
use App\Events\MessageSent;
use App\Events\ScheduledMessageStatusChanged;
use App\Models\ChatScheduledMessage;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class SendScheduledMessageAction
{
    // TODO: Move this to detected exceptions
    private array $errors = [
        'scheduled_message_not_found' => 'The requested scheduled message could not be found.',
        'scheduled_message_already_paused' => 'This scheduled message is already paused.',
        'user_not_found' => 'The specified user could not be found.',
        'model_not_found' => 'The requested data model could not be found.',
        'chat_not_found' => 'The requested chat conversation could not be found.',
    ];

    public function execute(ChatScheduledMessage $chatScheduledMessage): void
    {
        try {
            DB::transaction(function () use ($chatScheduledMessage) {

                if ($chatScheduledMessage->is_paused) {
                    throw new \Exception($this->errors['scheduled_message_already_paused']);
                }

                $scheduledMessage = $chatScheduledMessage->scheduledMessage;
                $user = $scheduledMessage->user;
                $chat = $chatScheduledMessage->chat;

                if (! $user) {
                    throw new \Exception($this->errors['user_not_found']);
                }

                $aiModel = $user->aiModels()->useable()->first();

                if (! $aiModel) {
                    throw new \Exception($this->errors['model_not_found']);
                }

                if (! $chat) {
                    throw new \Exception($this->errors['chat_not_found']);
                }

                $receivers = $chat->participants;

                $prompt = <<<PROMPT
                        MESSAGE REQUIREMENTS
                        Topic:
                        {$chatScheduledMessage->scheduledMessage->topic}
                        Description:
                        {$chatScheduledMessage->scheduledMessage->description}
                        PROMPT;

                $newMessage = (new ScheduledMessageAgent($user, $aiModel, $chatScheduledMessage->scheduledMessage))
                    ->prompt($prompt, model: $aiModel->name->value)->text;

                $message = Message::create([
                    'sender_id' => $user->id,
                    'chat_id' => $chatScheduledMessage->chat->id,
                    'message' => $newMessage,
                ]);

                $chatScheduledMessage->update([
                    'status' => ScheduledMessageStatusEnum::SENT,
                    'sent_at' => now(),
                    'last_attempt_at' => now(),
                    'message' => $newMessage,
                    'note' => 'message sent successfully',
                ]);

                broadcast(new ScheduledMessageStatusChanged($user->id, $chatScheduledMessage->refresh()));

                $receivers->each(fn ($receiver) => broadcast(new MessageSent($receiver->id, $message)));
            });
        } catch (\Throwable $exception) {

            $chatScheduledMessage->note = 'unable to send scheduled message';

            if (in_array($exception->getMessage(), $this->errors)) {
                $chatScheduledMessage->note = $exception->getMessage();
            }

            $chatScheduledMessage->status = ScheduledMessageStatusEnum::FAILED;
            $chatScheduledMessage->last_attempt_at = now();
            $chatScheduledMessage->save();

            throw $exception;
        }
    }
}
