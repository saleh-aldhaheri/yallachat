@props(['variant' => 'box'])

<input {{ $attributes->merge(['type' => 'text', 'class' => $variant === 'underline'
    ? 'w-full px-4 bg-transparent border-b border-border text-ink font-mono text-sm py-2 focus:outline-none focus:border-them transition-colors placeholder:text-ink-muted/40'
    : 'bg-panel border border-border rounded-lg px-3 py-2 text-xs font-mono text-ink outline-none focus:border-them/60 placeholder:text-ink-muted/60 min-w-0']) }}>
