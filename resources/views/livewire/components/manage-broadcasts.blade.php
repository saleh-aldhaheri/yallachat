<div>
    {{-- Flash --}}
    <x-flash-message class="mb-4"/>

    {{-- Toolbar --}}
    <div class="flex items-center gap-3 mb-4 flex-wrap">
        <x-filter-select wire:model.live="perPage"
                         :options="[10 => '10 per page', 25 => '25 per page', 50 => '50 per page', 100 => '100 per page']"/>

        <x-search-input wire:model.live.debounce.300ms="search" placeholder="search chat or topic..."
                        class="flex-1 max-w-[240px]"/>

        <x-filter-select wire:change="handleFilter($event.target.value)"
                         default-option="All statuses"
                         :options="collect($statuses)->mapWithKeys(fn ($status) => [$status->value => str_replace('_', ' ', $status->value)])->all()"/>

        <span class="ml-auto text-xs font-mono text-ink-muted">{{ $this->chatScheduledMessages->total() }} chats</span>
    </div>

    {{-- Table --}}
    <div class="bg-panel border border-border rounded-2xl overflow-x-auto">
        <div class="min-w-[780px]">
            <div class="grid grid-cols-[1.8fr_1.6fr_1fr_1.1fr_1.1fr_0.7fr] items-center px-5 py-3 bg-page/40 text-[10px] font-mono uppercase tracking-[0.08em] text-ink-muted">
                <span>Topic</span>
                <span>Chat</span>
                <span>Status</span>
                <span>Last attempt</span>
                <span>Last sent</span>
                <span class="text-right">Actions</span>
            </div>

            @forelse($this->chatScheduledMessages as $message)
                @php
                    $statusValue = $message->status?->value ?? 'pending';
                    $chatName = $this->chatName($message);
                @endphp
                <div class="grid grid-cols-[1.8fr_1.6fr_1fr_1.1fr_1.1fr_0.7fr] items-center px-5 py-3 text-[13px] border-t border-border hover:bg-page/40 transition-colors">
                    <span class="text-ink font-mono truncate pr-4">{{ $this->scheduledMessage->topic }}</span>

                    <span class="flex items-center gap-2.5 min-w-0">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-mono shrink-0 {{ $statusValue === 'sent' ? 'bg-them/15 text-them' : 'bg-page border border-border text-ink-muted' }}">
                            {{ $this->chatInitials($chatName) }}
                        </span>
                        <span class="text-ink font-mono truncate">{{ $chatName }}</span>
                    </span>

                    <span>
                        <x-badge tooltip="{{ $message->is_paused
                            ? \App\Enums\ScheduledMessageStatusEnum::PAUSED->description()
                            : ($message->status?->description() ?? '') }}"
                                 class="text-[11px] cursor-help {{
                                    $message->is_paused ? \App\Enums\ScheduledMessageStatusEnum::PAUSED->color() .' '.\App\Enums\ScheduledMessageStatusEnum::PAUSED->fontColor()
                                    : $message->status?->color().' '.$message->status?->fontColor() }}">
                            {{ str_replace('_', ' ',$message->is_paused? \App\Enums\ScheduledMessageStatusEnum::PAUSED->value : $statusValue) }}
                        </x-badge>
                    </span>

                    <span class="text-ink-muted font-mono">{{ $message->last_attempt_at?->format('Y-m-d H:i') ?? '—' }}</span>
                    <span class="text-ink-muted font-mono">{{ $message->sent_at?->format('Y-m-d H:i') ?? '—' }}</span>

                    <span class="text-right">
                        <x-btn-primary wire:click="view({{ $message->id }})">view</x-btn-primary>
                        @if($message->is_paused)
                            <x-btn-primary wire:click="pause({{ $message->id }})">Resume</x-btn-primary>
                        @else
                            <x-btn-primary wire:click="pause({{ $message->id }})">Pause</x-btn-primary>
                            @if($message->status === \App\Enums\ScheduledMessageStatusEnum::FAILED)
                                @if($this->sendingId === $message->id)
                                    <x-btn-primary disabled>Sending...</x-btn-primary>
                                @else
                                    <x-btn-primary wire:click="send({{ $message->id }})">Retry</x-btn-primary>
                                @endif
                            @elseif($message->status === \App\Enums\ScheduledMessageStatusEnum::OVERDUE)
                                @if($this->sendingId === $message->id)
                                    <x-btn-primary disabled>Sending...</x-btn-primary>
                                @else
                                    <x-btn-primary wire:click="send({{ $message->id }})">Sent Now</x-btn-primary>
                                @endif
                            @endif
                        @endif
                    </span>
                </div>
            @empty
                <x-empty-state class="border-t border-border"
                               title="No broadcasts found"
                               description="No chats are connected to this scheduled message yet."/>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($this->chatScheduledMessages->hasPages())
        <div class="flex items-center justify-between mt-5">
            <span class="text-xs font-mono text-ink-muted">
                {{ $this->chatScheduledMessages->firstItem() }}–{{ $this->chatScheduledMessages->lastItem() }} of {{ $this->chatScheduledMessages->total() }}
            </span>
            <div class="flex gap-2">
                @if($this->chatScheduledMessages->onFirstPage())
                    <x-btn-primary disabled>prev</x-btn-primary>
                @else
                    <x-btn-primary wire:click="previousPage">prev</x-btn-primary>
                @endif
                @if($this->chatScheduledMessages->hasMorePages())
                    <x-btn-primary wire:click="nextPage">next</x-btn-primary>
                @else
                    <x-btn-primary disabled>next</x-btn-primary>
                @endif
            </div>
        </div>
    @endif

    {{-- View overlay --}}
    @if($viewing)
        <x-view-modal title="{{ $this->scheduledMessage->topic }}" max-width="max-w-xl">
            @php
                $statusColors = $viewing->is_paused
                    ? \App\Enums\ScheduledMessageStatusEnum::PAUSED->color() . ' ' . \App\Enums\ScheduledMessageStatusEnum::PAUSED->fontColor()
                    : $viewing->status?->color() . ' ' . $viewing->status?->fontColor();

                $statusValue = $viewing->is_paused
                    ? \App\Enums\ScheduledMessageStatusEnum::PAUSED->value
                    : ($viewing->status?->value ?? '—');

                $statusTip = $viewing->is_paused
                    ? \App\Enums\ScheduledMessageStatusEnum::PAUSED->description()
                    : ($viewing->status?->description() ?? '');

                $rows = [
                    ['label' => 'chat', 'value' => $this->chatName($viewing), 'class' => 'text-right break-words'],
                    ['label' => 'status', 'badge' => true, 'value' => $statusValue, 'class' => $statusColors . ' cursor-help', 'title' => $statusTip],
                    ['label' => 'note', 'value' => $viewing->note ?? '—', 'class' => 'text-right max-w-[70%] break-words'],
                    ['label' => 'last_attempt_at', 'value' => $viewing->last_attempt_at?->format('Y-m-d H:i') ?? '—'],
                    ['label' => 'sent_at', 'value' => $viewing->sent_at?->format('Y-m-d H:i') ?? '—'],
                    ['label' => 'message', 'value' => $viewing->message ?? '—', 'class' => 'text-right max-w-[70%] leading-relaxed break-words'],
                ];
            @endphp
            <x-detail-list :rows="$rows"/>
        </x-view-modal>
    @endif
</div>
