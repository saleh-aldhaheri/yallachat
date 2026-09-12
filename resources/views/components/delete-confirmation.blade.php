@props(['title' => null, 'entity' => null, 'extra' => null, 'close' => 'closeDelete', 'confirm' => 'delete'])

<x-modal max-width="max-w-sm" :close="$close">
    <div class="text-center">
        <div class="flex justify-end mb-2">
            <x-btn-primary wire:click="{{ $close }}">X</x-btn-primary>
        </div>
        <p class="text-ink font-mono text-sm mb-2">{{ $title }}</p>
        <p class="text-ink-muted font-mono text-xs mb-6">
            Are you sure you want to delete
            @if($entity)<span class="text-them">{{ $entity }}</span>@endif?
            @if($extra){{ $extra }}@endif
        </p>
        <div class="flex justify-center gap-3">
            <x-btn-primary wire:click="{{ $close }}">cancel</x-btn-primary>
            <x-btn-primary class="text-danger" wire:click="{{ $confirm }}">delete</x-btn-primary>
        </div>
    </div>
</x-modal>
