@props(['title' => null, 'subtitle' => null])

<div class="min-h-screen flex justify-center items-center">
    <div class="max-w-md w-full">
        <div class="bg-panel border border-border rounded-xl p-8 md:p-10">
            <p class="text-ink text-center text-base font-mono mb-1"><span class="text-ink-muted">$</span> {{ $title }}</p>
            <p class="text-ink-muted text-center text-xs font-mono mb-8">{{ $subtitle }}</p>
            {{ $slot }}
            @isset($footer)
                <p class="mt-6 text-xs text-ink-muted text-center font-mono">{{ $footer }}</p>
            @endisset
        </div>
    </div>
</div>
