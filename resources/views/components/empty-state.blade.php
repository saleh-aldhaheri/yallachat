@props(['title' => null, 'description' => null, 'titleClass' => null, 'descriptionClass' => null])

<div {{ $attributes->merge(['class' => 'py-14 px-5 text-center']) }}>
    <p class="text-ink font-mono text-sm {{ $titleClass }}">{{ $title }}</p>
    @if($description)
        <p class="text-ink-muted font-mono text-xs mt-2 {{ $descriptionClass }}">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-5 flex justify-center">{{ $action }}</div>
    @endisset
</div>
