@props(['name' => null])

@if($name)
    @error($name)
        <p {{ $attributes->merge(['class' => 'text-[11px] text-danger font-mono mt-1.5']) }}>{{ $message }}</p>
    @enderror
@endif
