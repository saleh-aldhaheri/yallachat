<div class="grid grid-rows-[auto_1fr] lg:grid-rows-[80px_1fr] grid-cols-[minmax(0,1fr)] h-full min-h-0">
    <header class="min-w-0">
        <livewire:component.chat-area-header :connectedUsers="$connectedUsers" :chat="$chat" />
    </header>

    <section class="min-h-0 min-w-0">
        <livewire:component.chat-box
            :chat="$chat"
            :connectedUsers="$connectedUsers"
            wire:key="chat-box-{{ $chat->id }}"
        />
    </section>
</div>
