<?php

namespace App\Livewire\Component;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class ChatAvatar extends Component
{
    public Chat $chat;

    public ?Message $lastMessage = null;

    public ?User $participant = null;

    public int $unreadCount = 0;

    public int $openChatId = 0;

    public bool $hasNotificationChannels = false;

    public bool $isPriority = false;

    public function mount(): void
    {
        $this->lastMessage = $this->chat->messages()->latest()->first();
        $this->unreadCount = $this->chat->unreadCountForUser(auth()->id());

        if ($this->chat->type == 'private') {
            $this->participant = $this->chat
                ->participants()
                ->whereNot('participant_id', auth()->id())
                ->first();

            $this->refreshPriorityState();
        }
    }

    public function render(): View
    {
        return view('livewire.components.chat-avatar');
    }

    public function openChat(): void
    {
        $this->chat->refresh();
        if ($this->chat->deleted_at !== null) {
            $this->dispatch('refresh-chat-list');
        } else {
            $this->dispatch('chat-open', $this->chat);
        }
    }

    #[On('chat-open')]
    public function onChatOpen(Chat $chat): void
    {
        if ($chat->id === $this->chat->id) {
            $this->openChatId = $chat->id;
            $this->unreadCount = 0;
        } else {
            $this->openChatId = $chat->id;
        }
    }

    #[On('chat-close')]
    public function onChatClose(): void
    {
        $this->openChatId = 0;
        $this->unreadCount = $this->chat->unreadCountForUser(auth()->id());
    }

    public function getListeners(): array
    {
        $authId = auth()->id();
        $chatId = $this->chat->id;

        return [
            "echo-private:chat.{$chatId}.{$authId},.App\Events\MessageSent" => 'countNewMessages',
        ];
    }

    public function countNewMessages(): void
    {
        if ($this->openChatId === $this->chat->id) {
            $this->unreadCount = 0;

            return;
        }

        $this->unreadCount = $this->chat->unreadCountForUser(auth()->id());
    }

    public function removeChat(): void
    {
        $this->dispatch('remove-chat', $this->chat->id);
    }

    public function updatePriority(): void
    {
        if (! $this->hasNotificationChannels) {
            Toaster::warning('You need a connected notification channel to mark a chat as priority.');

            return;
        }

        $this->chat->setPriorityFor(auth()->user(), ! $this->isPriority);
        $this->isPriority = ! $this->isPriority;
        Toaster::success('Chat priority updated');
    }

    #[On('notification-channels-updated')]
    public function refreshPriorityState(): void
    {
        if ($this->chat->type !== 'private') {
            $this->hasNotificationChannels = false;
            $this->isPriority = false;

            return;
        }

        $this->hasNotificationChannels = auth()->user()->notificationChannels()->useable()->exists();

        $this->isPriority = $this->hasNotificationChannels
            && (bool) $this->chat->participants()
                ->wherePivot('participant_id', auth()->id())
                ->first()?->pivot?->is_priority;
    }
}
