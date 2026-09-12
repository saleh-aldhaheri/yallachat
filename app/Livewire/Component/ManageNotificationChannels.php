<?php

namespace App\Livewire\Component;

use App\Enums\NotificationChannels;
use Illuminate\View\View;
use Livewire\Component;

class ManageNotificationChannels extends Component
{
    public function render(): View
    {
        return view('livewire.components.manage-notification-channels', [
            'userNotificationChannels' => auth()->user()->notificationChannels()->get(),
            'notificationChannels' => NotificationChannels::cases(),
        ]);
    }
}
