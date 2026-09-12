@props(['service', 'currentService' => null])

<div class="bg-panel p-5 w-full transition-transform ease-in-out duration-500 hover:-translate-y-2">
    <div class="flex items-center justify-between">
        <div class="flex space-x-2 items-center">
            <img src="{{ $service->image() }}" class="h-10 w-10"/>
            <h5>{{ ucfirst($service->label()) }}</h5>
        </div>
        <div>
            @if ($currentService)
                <x-badge class="text-[11px] {{ $currentService->is_available ? 'bg-them/10 border-them text-them' : 'bg-danger/10 border-danger text-danger' }}">
                    {{ $currentService->is_available ? 'Connected' : 'Disconnected' }}
                </x-badge>
            @else
                <x-badge class="text-[11px] bg-ink-muted/10 border-ink-muted text-ink-muted">
                    Not Configured
                </x-badge>
            @endif
        </div>
    </div>

    <div class="p-3">
        <p class="text-ink-muted text-[11px] leading-loose">
            {{ $service->description() }}
        </p>
    </div>

    <div class="p-3 flex {{ $currentService?->is_available ? 'justify-between' : 'justify-end' }}">
        @if ($currentService)
            <x-toggle :active="$currentService->is_active"
                      wire:click="toggleActive({{ $currentService->id }})"
                      tooltip="{{ $currentService->is_active ? 'if active the agent will use the service' : 'if not the agent wont use the service' }}">
            </x-toggle>
        @endif

        @if ($currentService && ! $currentService->is_available)
            <div class="flex space-x-2">
                <x-tooltip text="this will delete the service" text-class="text-danger">
                    <x-btn-primary class="text-danger" wire:click="remove({{ $currentService->id }})">Remove</x-btn-primary>
                </x-tooltip>
                <x-tooltip text="connection to services failed connected again">
                    <x-btn-primary class="text-them" wire:click="configure('{{ $service->value }}')">Reconnect</x-btn-primary>
                </x-tooltip>
            </div>
        @elseif (! $currentService)
            <x-tooltip text="connect the service and enable your agent with better decisions">
                <x-btn-primary class="text-ink-muted" wire:click="configure('{{ $service->value }}')">Configure</x-btn-primary>
            </x-tooltip>
        @else
            <x-tooltip text="this will delete the service" text-class="text-danger">
                <x-btn-primary class="text-danger" wire:click="remove({{ $currentService->id }})">Remove</x-btn-primary>
            </x-tooltip>
        @endif
    </div>
</div>
