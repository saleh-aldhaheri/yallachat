@props([])

<div {{ $attributes->merge(['class' => "bg-panel p-4 sm:p-5 rounded-none flex flex-col gap-4 min-w-0"]) }}>
    <div class="space-y-2 min-w-0">
        {{ $header }}
    </div>
    @if (isset($badges) && $badges->isNotEmpty())
        <div class="flex flex-wrap gap-2">
            {{ $badges }}
        </div>
    @endif
    @if (isset($actions) && $actions->isNotEmpty())
        <div class="flex items-center justify-between gap-2 mt-auto">
            {{ $actions }}
            @if (isset($menu) && $menu->isNotEmpty())
                <x-actions-menu>{{ $menu }}</x-actions-menu>
            @endif
        </div>
    @endif
</div>
