<?php

namespace App\Observers;

use App\Models\NotificationChannel;
use App\Models\User;

class NotificationChannelObserver
{
    public function updated(NotificationChannel $notificationChannel): void
    {
        $this->clearPriorityIfNoUsableChannels($notificationChannel->user_id);
    }

    public function deleted(NotificationChannel $notificationChannel): void
    {
        $this->clearPriorityIfNoUsableChannels($notificationChannel->user_id);
    }

    private function clearPriorityIfNoUsableChannels(int $userId): void
    {
        $user = User::query()->find($userId);

        if (! $user || $user->notificationChannels()->useable()->exists()) {
            return;
        }

        $user->joinedChats()
            ->newPivotQuery()
            ->where('is_priority', true)
            ->update(['is_priority' => false]);
    }
}
