@props(['command' => null, 'flag' => null, 'arg' => null, 'description' => null])

<div class="space-y-5 pb-10">
    <p class="text-lg">
        <span class="text-them">$</span>
        @if($command)<span class="text-ink-muted">{{ $command }}</span>@endif
        @if($flag)<span class="text-you">{{ $flag }}</span>@endif
        @if($arg)<span class="text-ink">{{ $arg }}</span>@endif
    </p>
    <p class="text-ink-muted text-xl">{!! $description !!}</p>
</div>
