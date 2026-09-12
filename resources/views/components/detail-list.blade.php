@props(['rows' => []])

<dl class="space-y-4 text-xs font-mono">
    @foreach($rows as $row)
        <div class="flex justify-between {{ $loop->last ? '' : 'border-b border-border pb-2' }}">
            <dt class="text-ink-muted shrink-0">{{ $row['label'] }}</dt>
            @if (! empty($row['badge']))
                <dd>
                    <x-badge tooltip="{{ $row['title'] ?? null }}" class="{{ $row['class'] ?? '' }}">{{ $row['value'] }}</x-badge>
                </dd>
            @else
                <dd class="text-ink {{ $row['class'] ?? '' }}">{{ $row['value'] }}</dd>
            @endif
        </div>
    @endforeach
</dl>
