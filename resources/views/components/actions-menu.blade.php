@props([])

<div class="relative shrink-0" x-data="{ open: false }">
    <x-btn-primary x-on:click="open = !open" class="cursor-pointer">actions</x-btn-primary>
    <div x-show="open"
         x-cloak
         x-on:click="open = false"
         x-on:click.outside="open = false"
         x-on:keydown.escape.window="open = false"
         class="absolute right-0 top-full mt-2 z-50 w-40 bg-panel border border-border rounded-lg p-2 shadow-2xl space-y-1 [&>*]:w-full [&>*]:text-xs">
        {{ $slot }}
    </div>
</div>
