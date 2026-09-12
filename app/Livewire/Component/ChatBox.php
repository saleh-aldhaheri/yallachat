<?php

namespace App\Livewire\Component;

use App\Action\SendMessageAction;
use App\Action\TriggerAiToSendAction;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatBox extends Component
{
    public Chat $chat;

    public Collection $messages;

    public int $page = 1;

    public bool $hasMorePages = true;

    public bool $loadingMore = false;

    public string $message = '';

    public bool $isOpen = true;

    public array $connectedUsers = [];

    public function mount(): void
    {
        $this->messages = collect();
        $this->loadMessages();
        $this->markAsRead();
    }

    public function render(): View
    {
        return view('livewire.components.chat-box');
    }

    public function loadMessages(): void
    {
        $paginate = $this->chat->messages()
            ->latest()
            ->paginate(10, ['*'], 'page', $this->page);

        $newMessages = collect($paginate->items())->reverse()->values();
        $this->messages = $newMessages->concat($this->messages);
        $this->hasMorePages = $paginate->hasMorePages();
    }

    public function loadMore(): void
    {
        if (! $this->hasMorePages || $this->loadingMore) {
            return;
        }

        $this->loadingMore = true;
        $this->page++;
        $this->loadMessages();
        $this->loadingMore = false;
    }

    public function send(): void
    {
        if (! trim($this->message)) {
            return;
        }

        $newMessage = app(SendMessageAction::class)->execute($this->chat, $this->message);

        $participants = $this->chat->participants()->whereNot('participant_id', auth()->id())->get();
        $this->message = '';
        $this->messages->push($newMessage);
        $this->dispatch('scroll-to-bottom');

        if ($this->chat->type === 'private') {
            $offlineUser = $participants->first();
            if ($offlineUser && ! collect($this->connectedUsers)->contains('id', $offlineUser->id)) {
                app(TriggerAiToSendAction::class)->execute($offlineUser, $this->chat, auth()->user());
            }
        }
    }

    public function getListeners(): array
    {
        $authId = auth()->id();
        $chatId = $this->chat->id;

        return [
            "echo-private:chat.{$chatId}.{$authId},.App\Events\MessageSent" => 'newMessage',
        ];
    }

    public function newMessage($event): void
    {
        $message = Message::query()->find($event['message']['id']);
        $this->messages->push($message);

        if ($this->isOpen) {
            $this->markAsRead();
        }

        $this->dispatch('scroll-to-bottom');
    }

    #[On('chat-close')]
    public function onChatClose(): void
    {
        $this->isOpen = false;
    }

    #[On('connected-users-updated')]
    public function onConnectedUsersUpdated(array $connectedUsers): void
    {
        $this->connectedUsers = $connectedUsers;
    }

    private function markAsRead(): void
    {
        $latestMessage = $this->chat->messages()->latest()->first();

        if ($latestMessage) {
            DB::table('chat_participants')
                ->where('participant_id', auth()->id())
                ->where('chat_id', $this->chat->id)
                ->update(['last_read_message_id' => $latestMessage->id]);
        }
    }
}
