<?php

namespace App\Livewire\Component;

use Illuminate\View\View;
use Livewire\Component;

class UserSetting extends Component
{
    public function render(): View
    {
        return view('livewire.components.user-setting');
    }

    public function openProfile(): void
    {
        $this->dispatch('profile-open');
    }

    public function openService(): void
    {
        $this->dispatch('service-open');
    }

    public function openAi(): void
    {
        $this->dispatch('ai-open');
    }

    public function openScheduledMessages(): void
    {
        $this->dispatch('scheduled-messages-open');
    }

    public function openNotificationChannels(): void
    {
        $this->dispatch('notification-channels-open');
    }
}
