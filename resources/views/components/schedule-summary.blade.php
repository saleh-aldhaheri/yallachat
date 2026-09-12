<div {{ $attributes->merge(['class' => 'col-span-full mb-5 flex items-center justify-between bg-[var(--color-panel)] border border-border rounded-md px-4 py-3']) }}>
    <div class="text-[12.5px] text-ink font-mono">{{ $slot }}</div>
    <div class="text-[10.5px] font-mono text-ink-muted">derived from start date</div>
</div>
