<?php

namespace App\Livewire\Component;

use App\Models\Chat;
use App\Models\User;
use App\Services\SendNotificationService;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class ChatAreaHeader extends Component
{
    public User $participant;

    public Chat $chat;

    public ?bool $isOnline = false;

    public ?string $name = '';

    public ?string $image = '';

    public string $chatType = '';

    public ?bool $isPriority = null;

    public array $connectedUsers = [];

    #[Validate('required|min:3|max:500')]
    public string $urgentMessage = '';

    public function mount(): void
    {
        $this->participant = $this->chat
            ->participants()
            ->whereNot('participant_id', auth()->id())
            ->first();

        $this->chatType = $this->chat->type;
        $this->name = $this->chatType == 'private' ? $this->participant->name : $this->chat->name;
        $this->image = $this->chatType == 'private' ? $this->participant->avatar : $this->chat->avatar;
        $this->isPriority = (bool) $this->chat->participants()
            ->wherePivot('participant_id', $this->participant->id)
            ->first()?->pivot->is_priority;

        $this->updateStatus();
    }

    public function render(): View
    {
        return view('livewire.components.chat-area-header');
    }

    #[On('connected-users-updated')]
    public function onConnectedUsersUpdated(array $connectedUsers): void
    {
        $this->connectedUsers = $connectedUsers;
        $this->updateStatus();
    }

    private function updateStatus(): void
    {
        if ($this->chatType === 'private') {
            $this->isOnline = collect($this->connectedUsers)
                ->contains('id', $this->participant->id);
        }
    }

    #[On('notification-channels-updated')]
    public function refreshPriority(): void
    {
        if ($this->chatType !== 'private') {
            return;
        }

        $this->isPriority = (bool) $this->chat->participants()
            ->wherePivot('participant_id', $this->participant->id)
            ->first()?->pivot->is_priority;
    }

    public function sendUrgent(): void
    {
        $this->validate();

        if ($this->participant->notificationChannels()->useable()->count() < 1) {
            Toaster::warning("Sorry, {$this->participant->name} doesn't have any active notification channels.");
        } else {
            $service = new SendNotificationService;

            if ($service->send(auth()->user(), $this->participant, $this->urgentMessage)) {
                Toaster::success("Urgent notification sent to {$this->participant->name}.");
            } else {
                Toaster::error("You've reached the maximum attempts for {$this->participant->name}. Please wait {$service->cooldownForHumans()} before trying again.");
            }
        }
        $this->urgentMessage = '';
    }

    public function cancelUrgent(): void
    {
        $this->urgentMessage = '';
    }
}
