@props([
    'message',
    'isMine' => false,
])

<div class="flex flex-col {{ $isMine ? 'items-end' : 'items-start' }} gap-3 max-w-sm sm:max-w-md w-fit {{ $isMine ? 'ml-auto' : '' }}">
    <div class="w-full flex gap-2 min-w-0">
        <x-avatar :image="$message->user->avatar" />
        <div class="min-w-0">
            <h5 class="{{ $isMine ? 'text-you' : 'text-them' }} truncate">~/{{$isMine? "You" : $message->user->name}}</h5>
            <p class="text-ink-muted truncate">~/{{$message->user->email}}</p>
        </div>
    </div>

    <div class="w-full border-2 {{ $isMine ? 'border-you' : 'border-them' }} p-3.5 bg-panel/50 font-mono text-sm flex flex-col justify-between gap-2 shadow-sm">

        <p class="text-ink break-words leading-relaxed whitespace-pre-line">
            {{ $message->message }}
        </p>

        <span class="text-ink-muted text-[10px] text-right self-end select-none">
            {{ $message->created_at->diffForHumans() }}
        </span>

    </div>

</div>
