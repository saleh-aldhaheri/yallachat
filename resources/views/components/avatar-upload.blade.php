@props(['target' => null, 'error' => null, 'previewUrl' => null, 'avatarUrl' => null, 'initials' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center mb-6']) }}>
    <div class="relative group w-24 h-24 aspect-square">
        <div class="w-full h-full rounded-2xl border-2 border-them/40 overflow-hidden flex items-center justify-center shadow-[0_0_15px_rgba(63,185,80,0.15)] group-hover:border-them transition-all">
            @if($previewUrl)
                <img src="{{ $previewUrl }}" class="w-full h-full object-cover" />
            @elseif($avatarUrl)
                <img src="{{ $avatarUrl }}" class="w-full h-full object-cover" />
            @else
                <span class="font-mono text-xl text-them font-bold">{{ $initials }}</span>
            @endif
            <div class="absolute inset-0 bg-page/80 backdrop-blur-xs flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                <span class="text-them font-mono text-xs">[upload]</span>
            </div>
        </div>
        <input type="file" wire:model="{{ $target }}" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
    </div>
    <div class="mt-2 text-center">
        <div wire:loading wire:target="{{ $target }}" class="text-xs text-you font-mono animate-pulse">> uploading_img...</div>
        <x-form-error :name="$error"/>
    </div>
</div>
