<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center gap-3']) }}>
    <div class="w-6 h-6 border-2 border-them/30 border-t-them rounded-full animate-spin"></div>
    @if ($slot->isNotEmpty())
        <p class="text-xs text-ink-muted font-mono animate-pulse">{{ $slot }}</p>
    @endif
</div>