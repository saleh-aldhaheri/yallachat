@props(['message' => null, 'timeout' => 3000, 'label' => null, 'variant' => 'success'])

@php
    $msg = $message ?? ($variant === 'error' ? session('failed') : session('status'));
    $label ??= $variant === 'error' ? '[ERROR]' : '[SUCCESS]';
    $colors = $variant === 'error'
        ? 'border-danger/40 bg-danger/10 text-danger'
        : 'border-them/40 bg-them/10 text-them';
@endphp

@if($msg)
    <div @if($timeout) x-data="{show: true}" x-init="setTimeout(() => show = false, {{ $timeout }})"
         x-show="show" x-transition.opacity.duration.500ms @endif
         {{ $attributes->merge(['class' => 'p-3 rounded border '.$colors.' text-xs font-mono text-center']) }}>
        <span class="font-bold">{{ $label }}</span> {{ $msg }}
    </div>
@endif
