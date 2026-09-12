@props(['tooltip' => null, 'dotClass' => null])

<x-tooltip :text="$tooltip">
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full border font-mono w-fit px-2.5 py-1']) }}>
        @if ($dotClass)
            <span class="h-2 w-2 inline-block rounded-full {{ $dotClass }}"></span>
        @endif
        {{ $slot }}
    </span>
</x-tooltip>
