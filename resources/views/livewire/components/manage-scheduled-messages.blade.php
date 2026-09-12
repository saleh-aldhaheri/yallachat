<div class="grid grid-rows-[auto_1fr] lg:grid-rows-[80px_1fr] grid-cols-[minmax(0,1fr)] px-4 sm:px-10">
    <x-panel-header title="Scheduled-Messages">
        <x-btn-primary class="lg:hidden" wire:click="$dispatch('go-back')">←</x-btn-primary>
        <x-slot:menu>
            @if($page !== 'list_scheduled_messages')
                <x-btn-primary wire:click="setPage('list_scheduled_messages')">
                    List Scheduled Messages
                </x-btn-primary>
            @endif
            @if($page !== 'create_scheduled_message')
                <x-btn-primary wire:click="setPage('create_scheduled_message')">
                    Schedule Message
                </x-btn-primary>
            @endif
        </x-slot:menu>
    </x-panel-header>
    <section class="p-3 sm:p-5 min-w-0">
        @if($page === "create_scheduled_message")
            <x-command-header command="scheduled-message" flag="--add"
                              description="Create a new scheduled message and configure when it should be delivered, whether it runs on a recurring schedule or at specific dates."/>
            <livewire:component.handle-scheduled-message />
        @elseif($page === "update_scheduled_message" && $scheduledMessage)
            <x-command-header command="scheduled-message" flag="--update" :arg="$scheduledMessage->topic"
                              description="Editing <span class='text-ink'>{{ $scheduledMessage->topic }}</span>. Changes apply immediately on save."/>
            <livewire:component.handle-scheduled-message :scheduled-message="$scheduledMessage" />
        @elseif($page === "manage_broadcasts" && $scheduledMessage)
            <x-command-header command="scheduled-message" flag="--broadcasts" :arg="$scheduledMessage->topic"
                              description="Broadcasts connect this scheduled message to its target chats. Here you can track the delivery status of every chat, retry failed sends, and manage each broadcast individually."/>
            <livewire:component.manage-broadcasts :scheduled-message="$scheduledMessage" />
        @else
            <x-command-header command="scheduled-message" flag="--list"
                              description="View and manage all scheduled messages in this workspace. Track Message delivery status, including pending, sent, failed, and cancelled messages."/>
            <livewire:component.list-scheduled-messages />
        @endif
    </section>
</div>
