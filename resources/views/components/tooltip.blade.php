@props(['text' => null, 'textClass' => 'text-ink-muted'])

<span x-data="{show: false}" class="relative inline-block">
    <span x-on:mouseenter="show = true" x-on:mouseleave="show = false">
        {{ $slot }}
    </span>
    @if ($text !== null)
        <span x-show="show" x-transition.opacity.duration.100
              class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2.5 py-1.5 rounded-md bg-page border border-border text-[10.5px] font-mono w-48 text-center whitespace-normal leading-relaxed z-20 pointer-events-none {{ $textClass }}">
            {{ $text }}
        </span>
    @endif
</span>
