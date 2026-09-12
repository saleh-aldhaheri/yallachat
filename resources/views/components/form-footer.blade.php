@props(['cancelAction' => null, 'cancelLabel' => 'Cancel'])

<div {{ $attributes->merge(['class' => 'flex items-center justify-end gap-3 px-6 py-4 border-t border-border/60 bg-[var(--color-panel)]']) }}>
    @if($cancelAction)
        <button type="button" wire:click="{{ $cancelAction }}"
                class="px-4 py-2 rounded-md text-xs font-mono text-ink-muted hover:text-ink border border-transparent hover:border-border transition-all cursor-pointer bg-transparent">
            {{ $cancelLabel }}
        </button>
    @endif
    {{ $slot }}
</div>
