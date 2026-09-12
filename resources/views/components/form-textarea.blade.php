@props(['resizable' => false])

<textarea {{ $attributes->merge(['class' => 'w-full bg-[var(--color-panel)] border border-border rounded-md px-3 py-2.5 text-ink font-mono text-[13px] outline-none focus:border-them/60 transition-colors placeholder:text-ink-muted/40'.($resizable ? ' resize-y min-h-[88px] leading-relaxed' : ' resize-none')]) }}>
</textarea>
