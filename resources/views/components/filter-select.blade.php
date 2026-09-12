@props(['defaultOption' => null, 'options' => []])

<select {{ $attributes->merge(['class' => 'bg-panel border border-border rounded-lg px-3 py-2 text-xs font-mono text-ink outline-none focus:border-them/60 cursor-pointer shrink-0']) }}>
    @if ($defaultOption !== null)
        <option value="" class="bg-panel">{{ $defaultOption }}</option>
    @endif
    @foreach ($options as $value => $label)
        <option value="{{ is_string($value) ? $value : $label }}" class="bg-panel">{{ is_string($value) ? $label : $value }}</option>
    @endforeach
    {{ $slot }}
</select>
