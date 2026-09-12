@props(['user' => null])

<div {{ $attributes->merge(['class' => 'py-5 flex items-center gap-5 min-w-0']) }}>
    <x-avatar :image="$user?->avatar" class="shrink-0" />
    <div class="flex-1 min-w-0">
        <p class="truncate">{{ $user->name }}</p>
        <p class="truncate">{{ $user->email }}</p>
    </div>
    {{ $slot }}
</div>
