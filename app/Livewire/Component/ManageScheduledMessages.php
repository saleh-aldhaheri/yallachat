<?php

namespace App\Livewire\Component;

use App\Models\ScheduledMessage;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageScheduledMessages extends Component
{
    public string $page = 'list_scheduled_messages';

    public ?ScheduledMessage $scheduledMessage = null;

    public function render()
    {
        return view('livewire.components.manage-scheduled-messages');
    }

    #[On('set-page')]
    public function setPage(string $page): void
    {
        if ($page !== 'update_scheduled_message' && $page !== 'manage_broadcasts') {
            $this->scheduledMessage = null;
        }

        $this->page = $page;
    }

    #[On('set-scheduled-message')]
    public function setScheduledMessage(ScheduledMessage $scheduledMessage): void
    {
        $this->scheduledMessage = $scheduledMessage;
    }
}
