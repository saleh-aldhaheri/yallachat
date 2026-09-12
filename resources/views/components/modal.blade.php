@props(['maxWidth' => 'max-w-sm', 'close' => null, 'scrollable' => false])

<div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-black/60" @if($close) wire:click="{{ $close }}" @endif></div>
    <div @class([
        'relative z-10 w-full bg-panel border border-border rounded-2xl p-5 sm:p-8 shadow-2xl',
        $maxWidth,
        'max-h-[90vh] overflow-y-auto no-scrollbar' => $scrollable,
    ])>
        {{ $slot }}
    </div>
</div>
