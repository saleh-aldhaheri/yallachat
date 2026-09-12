@props(['active' => false, 'size' => 'sm'])

@php
    $sizes = ['sm' => 'h-2.5 w-2.5', 'md' => 'h-3 w-3', 'lg' => 'h-5 w-5'];
@endphp

<span class="inline-block rounded-full {{ $sizes[$size] ?? $size }} {{ $active ? 'bg-them' : 'bg-danger' }}"></span>
