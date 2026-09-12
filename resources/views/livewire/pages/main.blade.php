<div class="flex flex-col h-screen lg:grid lg:grid-cols-[1fr_2fr]">
    <div @class([
        'flex flex-col min-h-0 min-w-0 flex-1 border-2 border-border overflow-hidden',
        'hidden lg:flex' => $page !== 'chat' || $chat,
    ])>
        <livewire:component.sidebar/>
    </div>

    <div @class([
        'flex-1 min-h-0 min-w-0 border-2 border-border overflow-y-auto overflow-x-hidden no-scrollbar',
        'hidden lg:block' => $page === 'chat' && ! $chat,
    ])>
        @if($chat && $page === "chat")
            <livewire:component.chat-area :connected-users="$connectedUsers" :chat="$chat" wire:key="chat-area-{{ $chat->id }}" />
        @elseif($page === "ai")
               <livewire:component.manage-ai-model/>
        @elseif($page === "service" || session('service-connected'))
                @php $this->openService() @endphp
                <livewire:component.manage-services/>
        @elseif($page === "scheduled_messages")
                <livewire:component.manage-scheduled-messages />
        @elseif($page === "profile")
                <livewire:component.profile/>
        @elseif($page === "notification_channels")
                <livewire:component.manage-notification-channels />
        @else
            <div class="h-full flex justify-center items-center">
                <x-default />
            </div>
        @endif
    </div>

    @if(! $chat || $page !== 'chat')
        <x-mobile-tab-bar :page="$page"/>
    @endif
</div>
@script
    <script>
        Echo.join('online')
            .here(users => {
                $wire.set('connectedUsers', users)
            })
            .joining(user => {
                $wire.joining(user)
            })
            .leaving(user => {
                $wire.leaving(user)
            });
    </script>
@endscript
