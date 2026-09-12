@props([
    'image' => null,
])

@if ($image)
    <img
        src="{{ $image }}"
        {{ $attributes->merge([
            'class' => 'rounded-2xl w-10 h-10 object-cover'
        ]) }}
    />
@else
    <div
        {{ $attributes->merge([
            'class' => 'rounded-2xl w-10 h-10 bg-ink-muted/20 flex items-center justify-center'
        ]) }}
    >
        <svg
            class="w-6 h-6 text-ink-muted"
            fill="currentColor"
            viewBox="0 0 20 20"
        >
            <path d="M10 10a4 4 0 100-8 4 4 0 000 8z" />
            <path d="M2 18a8 8 0 0116 0H2z" />
        </svg>
    </div>
@endif
