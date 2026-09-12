@props(['title' => null, 'close' => 'closeView', 'maxWidth' => 'max-w-lg', 'scrollable' => true])

<x-modal :max-width="$maxWidth" :close="$close" :scrollable="$scrollable">
    <div class="flex justify-between items-center mb-6">
        <h5 class="text-them font-mono text-base">{{ $title }}</h5>
        <x-btn-primary wire:click="{{ $close }}">X</x-btn-primary>
    </div>
    {{ $slot }}
</x-modal>
