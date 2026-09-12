<div class="relative">
    {{-- Search + Filter --}}
    <div class="mb-6 flex items-center gap-3">
        <x-search-input wire:model.live.debounce.300ms="search" placeholder="Search by topic or description..."
                        class="flex-1 px-4 py-2.5 text-sm"/>

        <x-filter-select wire:change="handleActiveFilter($event.target.value)"
                         :options="['active' => 'Active', 'inactive' => 'Inactive']"
                         default-option="All"/>
    </div>

    {{--  Scheduled Messages List  --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($scheduledMessages as $message)
            <x-list-card>
                <x-slot:header>
                    <div class="flex items-center space-x-3">
                        <div class="h-2.5 w-2.5 rounded-full {{ $message->is_active ? 'bg-them' : 'bg-danger' }}"></div>
                        <h2 class="text-ink text-lg truncate">{{ $message->topic }}</h2>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <p class="text-ink-muted text-xs">{{ $message->is_active ? 'active' : 'paused' }}</p>
                        <span class="text-ink-muted/40">·</span>
                        <p class="text-ink-muted text-xs truncate">{{ $message->description }}</p>
                    </div>
                </x-slot>

                <x-slot:actions>
                    <x-toggle :active="$message->is_active" wire:click="toggleActive({{ $message->id }})"
                              tooltip="toggle whether this scheduled message is delivered"/>
                </x-slot:actions>

                <x-slot:menu>
                    <x-btn-primary wire:click="broadcast({{ $message->id }})">broadcasts</x-btn-primary>
                    <x-btn-primary wire:click="update({{ $message->id }})">update</x-btn-primary>
                    <x-btn-primary wire:click="view({{ $message->id }})">view</x-btn-primary>
                    <x-btn-primary wire:click="showDelete({{ $message->id }})">delete</x-btn-primary>
                </x-slot:menu>
            </x-list-card>
        @empty
            <x-empty-state class="bg-panel rounded-3xl col-span-full"
                           title="No scheduled messages yet"
                           description="Schedule your first message to have the agent deliver it automatically."/>
        @endforelse
    </div>

    @if($hasMorePages)
        <div class="flex justify-center mt-6">
            <x-btn-primary wire:click="loadMore">load more</x-btn-primary>
        </div>
    @endif

    {{-- View overlay --}}
    @if($isViewShow && $scheduledMessage)
        <x-view-modal title="{{ $scheduledMessage->topic }}" max-width="max-w-2xl">
            @php
                $rows = [
                    ['label' => 'description', 'value' => $scheduledMessage->description, 'class' => 'text-right max-w-[70%] leading-relaxed break-words'],
                    ['label' => 'schedule_type', 'value' => str_replace('_', ' ', $scheduledMessage->schedule_type), 'class' => 'capitalize'],
                ];

                if ($scheduledMessage->schedule_type === 'recurring') {
                    $rows[] = ['label' => 'starts_on', 'value' => $scheduledMessage->start_date?->format('Y-m-d')];
                    $rows[] = ['label' => 'frequency', 'value' => $this->scheduleSummary($scheduledMessage), 'class' => 'text-right'];
                    $rows[] = ['label' => 'ends', 'value' => match ($scheduledMessage->ends_type ?? 'never') {
                        'on_date' => $scheduledMessage->ends_on?->format('Y-m-d') ?? '—',
                        default => 'Never',
                    }, 'class' => 'text-right'];
                } else {
                    $rows[] = ['label' => 'run_dates', 'value' => implode(', ', $scheduledMessage->run_dates ?? []), 'class' => 'text-right'];
                }

                $rows = array_merge($rows, [
                    ['label' => 'run_at', 'value' => substr((string) $scheduledMessage->run_at, 0, 5)],
                    ['label' => 'languages', 'value' => $scheduledMessage->languages ? implode(', ', $scheduledMessage->languages) : '—', 'class' => 'text-right'],
                    ['label' => 'active', 'value' => $scheduledMessage->is_active ? 'Yes' : 'No'],
                    ['label' => 'chats', 'value' => $this->chatNames($scheduledMessage), 'class' => 'text-right max-w-[60%] break-words'],
                ]);
            @endphp
            <x-detail-list :rows="$rows"/>
        </x-view-modal>
    @endif

    {{-- Delete confirmation overlay --}}
    @if($isDeleteShow && $scheduledMessage)
        <x-delete-confirmation title="delete scheduled message" entity="{{ $scheduledMessage->topic }}" extra="This cannot be undone."/>
    @endif

</div>
