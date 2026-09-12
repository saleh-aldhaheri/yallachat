@props(['label' => null, 'name' => null])

<div {{ $attributes->merge(['class' => 'mb-5']) }}>
    @if($label)
        <label class="block text-xs text-ink-muted font-mono mb-1.5"><span class="text-ink-muted">$</span> {{ $label }}</label>
    @endif
    {{ $slot }}
    @if($name)
        @error($name)
            <p class="text-[11px] text-danger font-mono mt-1.5">{{ $message }}</p>
        @enderror
    @endif
</div>
