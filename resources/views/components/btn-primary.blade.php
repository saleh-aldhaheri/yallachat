@props(['disabled' => false])

<div {{ $attributes->merge(['class' => 'btn-primary'.($disabled ? ' opacity-40 cursor-default' : '')]) }}>
    [{{ $slot }}]
</div>
