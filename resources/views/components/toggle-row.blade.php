@props(['title' => null, 'description' => null])

<div {{ $attributes->merge(['class' => 'flex items-center justify-between bg-[var(--color-panel)] border border-border rounded-md px-4 py-3']) }}>
    <div class="flex flex-col gap-0.5">
        <div class="text-[12.5px] text-ink font-mono font-medium">{{ $title }}</div>
        @if($description)
            <div class="text-[11px] text-ink-muted font-mono">{{ $description }}</div>
        @endif
    </div>
    {{ $slot }}
</div>
