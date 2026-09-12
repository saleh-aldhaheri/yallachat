@props(['loadingLabel' => 'loading...', 'type' => 'submit'])

<button type="{{ $type }}"
        wire:loading.attr="disabled"
        {{ $attributes->merge(['class' => 'px-5 py-2 rounded-md text-xs font-mono font-semibold bg-them text-page hover:brightness-110 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    <span wire:loading.remove>{{ $slot }}</span>
    <span wire:loading class="flex items-center justify-center gap-1.5">
        <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        {{ $loadingLabel }}
    </span>
</button>
