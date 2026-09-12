@props(['title' => null])

<header class="flex items-center gap-2 p-3 sm:p-5 border-b-2 border-border min-w-0">
    <h5 class="text-them truncate min-w-0 flex-1 text-sm sm:text-base"><img src="/images/yalla-chat-icon-dark.svg" alt="" class="inline-block h-4 w-4 sm:h-5 sm:w-5 rounded-md align-[-2px] mr-1">~/{{ config('app.name') }}/{{ $title }}</h5>

    <div class="flex items-center gap-1.5 sm:gap-2 shrink-0 [&>*]:text-[11px] sm:[&>*]:text-sm">
        {{ $slot }}
    </div>

    @isset($menu)
        <div class="relative shrink-0" x-data="{ open: false }">
            <x-btn-primary x-on:click="open = !open" class="cursor-pointer">☰</x-btn-primary>
            <div x-show="open"
                 x-cloak
                 x-on:click.outside="open = false"
                 x-on:keydown.escape.window="open = false"
                 class="absolute right-0 top-full mt-2 z-50 w-48 bg-panel border border-border rounded-lg p-2 shadow-2xl space-y-1 [&>*]:w-full [&>*]:text-xs">
                {{ $menu }}
            </div>
        </div>
    @endisset
</header>
