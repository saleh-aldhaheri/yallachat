<div class="grid grid-rows-[auto_1fr] lg:grid-rows-[80px_1fr] grid-cols-[minmax(0,1fr)] px-4 sm:px-10">
    <x-panel-header title="Notification-Channels">
        <x-btn-primary class="lg:hidden" wire:click="$dispatch('go-back')">←</x-btn-primary>
    </x-panel-header>
    <section class="p-3 sm:p-5 min-w-0">
        <x-command-header command="notification-channels" flag="--manage"
                          description="Configure where the system sends your notifications."/>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($notificationChannels as $notificationChannel)
                @php
                    $currentNotificationChannel = $userNotificationChannels->firstWhere('type', $notificationChannel);
                @endphp

                @if($notificationChannel === \App\Enums\NotificationChannels::EMAIL)
                    <livewire:component.email-channel-configuration :notification-channel="$currentNotificationChannel" />
                @endif

                @if($notificationChannel === \App\Enums\NotificationChannels::WHATSAPP)
                    <livewire:component.whatsapp-channel-configuration :notification-channel="$currentNotificationChannel"/>
                @endif

            @endforeach
        </div>
    </section>
</div>
