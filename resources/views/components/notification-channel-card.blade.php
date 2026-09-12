@props(['notificationChannel', 'currentNotificationChannel' => null])
@php
    $isConnected = $currentNotificationChannel?->status === \App\Enums\NotificationChannelStatus::CONNECTED;
@endphp

<div class="bg-panel p-5 w-full transition-transform ease-in-out duration-500 hover:-translate-y-2">
    <div class="flex items-center justify-between">
        <div class="flex space-x-2 items-center">
            <img src="{{ $notificationChannel->image() }}" class="h-10 w-10"/>
            <h5>{{ $notificationChannel->label() }}</h5>
        </div>
        <div>
            @if ($currentNotificationChannel)
                <x-badge class="text-[11px] {{ $currentNotificationChannel->status->color() }} {{ $isConnected ? 'border-them text-them' : 'border-danger text-danger' }}">
                    {{ $currentNotificationChannel->status->label() }}
                </x-badge>
            @else
                <x-badge class="text-[11px] bg-ink-muted/10 border-ink-muted text-ink-muted">
                    {{ \App\Enums\NotificationChannelStatus::NOT_CONFIGURED->label() }}
                </x-badge>
            @endif
        </div>
    </div>

    <div class="p-3">
        <p class="text-ink-muted text-[11px] leading-loose">
            {{ $notificationChannel->description() }}
        </p>
    </div>

    <div class="p-3 flex justify-between">
        @if ($currentNotificationChannel)
            <x-toggle :active="$currentNotificationChannel->is_active"
                      wire:click="toggleActive"
                      tooltip="{{ $currentNotificationChannel->is_active ? 'Disable to mute notifications on this channel' : 'Enable to receive notifications on this channel' }}">
            </x-toggle>
        @endif

        @if ($currentNotificationChannel && ! $isConnected)
            <div class="flex space-x-2">
                <x-tooltip text="Remove this notification channel" text-class="text-danger">
                    <x-btn-primary class="text-danger" wire:click="remove">Remove</x-btn-primary>
                </x-tooltip>
                <x-tooltip text="Connection failed. Reconnect to resume receiving alerts">
                    <x-btn-primary class="text-them" wire:click="reconnect">Reconnect</x-btn-primary>
                </x-tooltip>
            </div>
        @elseif (! $currentNotificationChannel)
            <x-tooltip text="Configure this channel to receive notifications and alerts">
                <x-btn-primary class="text-ink-muted" wire:click="configure">Configure</x-btn-primary>
            </x-tooltip>
        @else
            <x-tooltip text="Remove this notification channel" text-class="text-danger">
                <x-btn-primary class="text-danger" wire:click="remove">Remove</x-btn-primary>
            </x-tooltip>
        @endif
    </div>
</div>
