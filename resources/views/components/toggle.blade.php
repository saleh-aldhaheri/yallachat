@props(['active' => false, 'label' => false, 'onLabel' => 'active', 'offLabel' => 'inactive', 'tooltip' => null, 'tooltipClass' => 'text-ink-muted'])

<x-tooltip :text="$tooltip" :text-class="$tooltipClass">
    <div class="flex items-center gap-2">
        <button type="button"
                {{ $attributes->merge(['class' => 'relative w-10 h-5 rounded-full transition-colors duration-200 focus:outline-none '.($active ? 'bg-them' : 'bg-ink-muted/30')]) }}>
            <span class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-ink transition-transform duration-200 {{ $active ? 'translate-x-5' : '' }}"></span>
        </button>
        @if ($label)
            <span class="text-sm font-mono {{ $active ? 'text-them' : 'text-ink-muted' }}">{{ $active ? $onLabel : $offLabel }}</span>
        @endif
    </div>
</x-tooltip>
