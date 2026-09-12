@props(['label' => 'Loading more...', 'show' => false])

@if($show)
    <div x-intersect="$wire.loadMore()" {{ $attributes->merge(['class' => 'p-4 text-center text-xs font-mono text-ink-muted']) }}>
        {{ $label }}
    </div>
@endif
