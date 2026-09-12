<?php

namespace App\Livewire\Pages;

use App\Models\Chat;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Main extends Component
{
    public ?Chat $chat = null;

    public $page = 'notification_channels';

    public array $connectedUsers = [];

    #[On('profile-open')]
    public function openProfile(): void
    {
        $this->page = 'profile';
    }

    #[On('chat-open')]
    public function openChat(Chat $chat): void
    {
        $this->page = 'chat';
        $this->chat = $chat;
    }

    #[On('chat-close')]
    public function closeChat(): void
    {
        $this->chat = null;
    }

    #[On('go-back')]
    public function goBack(): void
    {
        $this->page = 'chat';
        $this->chat = null;
    }

    #[On('ai-open')]
    public function openAi(): void
    {
        $this->page = 'ai';
    }

    #[On('service-open')]
    public function openService(): void
    {
        $this->page = 'service';
    }

    #[On('scheduled-messages-open')]
    public function openScheduledMessages(): void
    {
        $this->page = 'scheduled_messages';
    }

    #[On('notification-channels-open')]
    public function openNotificationChannels(): void
    {
        $this->page = 'notification_channels';
    }

    public function render(): View
    {
        return view('livewire.pages.main');
    }

    public function joining($user): void
    {
        $this->connectedUsers[] = $user;
        $this->dispatch('connected-users-updated', connectedUsers: $this->connectedUsers);
    }

    public function leaving($user): void
    {
        $this->connectedUsers = collect($this->connectedUsers)
            ->reject(fn ($u) => $u['id'] === $user['id'])
            ->values()
            ->all();
        $this->dispatch('connected-users-updated', connectedUsers: $this->connectedUsers);
    }
}
