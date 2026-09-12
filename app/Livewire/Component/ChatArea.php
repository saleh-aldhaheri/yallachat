<?php

namespace App\Livewire\Component;

use App\Models\Chat;
use Illuminate\View\View;
use Livewire\Component;

class ChatArea extends Component
{
    public Chat $chat;

    public array $connectedUsers = [];

    public function render(): View
    {
        return view('livewire.components.chat-area');
    }
}
