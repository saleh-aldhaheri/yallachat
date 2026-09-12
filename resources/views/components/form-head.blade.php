@props(['title' => null, 'subtitle' => null, 'status' => null, 'statusClass' => 'border-you/30 text-you bg-you/10'])

<div class="flex items-center justify-between px-6 py-4 border-b border-border/60">
    <div>
        <h2 class="text-ink text-sm font-mono font-bold">{{ $title }}</h2>
        @if($subtitle)
            <p class="text-ink-muted text-[11px] font-mono mt-0.5">{!! $subtitle !!}</p>
        @endif
    </div>
    @if($status)
        <span class="text-[10.5px] font-mono px-2.5 py-1 rounded-full border {{ $statusClass }}">
            {{ $status }}
        </span>
    @endif
</div>
