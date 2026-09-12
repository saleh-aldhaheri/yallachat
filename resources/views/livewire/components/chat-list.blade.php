<div class="h-full flex flex-col min-w-0 min-h-0">

    <div class="shrink-0 mb-2">
        <x-search-input variant="underline" wire:model.live.debounce.300ms="search" placeholder="Search..."/>
    </div>

    <div class="flex-1 overflow-y-auto no-scrollbar min-w-0 min-h-0">
        @foreach($chats as $chat)
            <livewire:component.chat-avatar :chat="$chat" wire:key="chat-{{ $chat->id }}"/>
        @endforeach

        <x-loading-more :show="$hasMorePages" label="Loading more chats..."/>
    </div>

</div>
