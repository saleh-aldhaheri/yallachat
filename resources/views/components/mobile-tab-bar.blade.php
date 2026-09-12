@props(['page' => 'chat'])

@php
    $tabs = [
        ['page' => 'chat', 'label' => 'Chat', 'event' => 'go-back'],
        ['page' => 'ai', 'label' => 'AI', 'event' => 'ai-open'],
        ['page' => 'service', 'label' => 'Services', 'event' => 'service-open'],
        ['page' => 'scheduled_messages', 'label' => 'Schedule', 'event' => 'scheduled-messages-open'],
        ['page' => 'profile', 'label' => 'Profile', 'event' => 'profile-open'],
    ];
@endphp

<nav {{ $attributes->merge(['class' => 'shrink-0 border-t-2 border-border bg-panel lg:hidden']) }}>
    <div class="flex">
        @foreach($tabs as $tab)
            <button type="button"
                    wire:click="$dispatch('{{ $tab['event'] }}')"
                    class="flex-1 py-3 text-[11px] font-mono transition-colors cursor-pointer {{ $page === $tab['page'] ? 'text-them' : 'text-ink-muted' }}">
                {{ $tab['label'] }}
            </button>
        @endforeach
    </div>
</nav>
