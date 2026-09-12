<div class="grid grid-rows-[auto_1fr_auto] lg:grid-rows-[80px_1fr_80px] h-full min-w-0 min-h-0">

    <x-panel-header title="{{ Str::ucfirst($panel) }}">
        <x-slot:menu>
            <x-btn-primary wire:click="setPanel('chat')">Chat</x-btn-primary>
            <x-btn-primary wire:click="setPanel('user')">Add User</x-btn-primary>
            <x-btn-primary wire:click="setPanel('group')">Add Group</x-btn-primary>
        </x-slot:menu>
    </x-panel-header>

    <section class="h-full min-h-0 min-w-0 p-3 sm:p-5">
        @if($panel === 'user')
            <livewire:component.user-list wire:navigate />
        @elseif($panel === 'group')
           <livewire:component.create-group />
        @else
            <livewire:component.chat-list wire:navigate/>
        @endif
    </section>

    <footer class="hidden lg:flex items-center p-3 sm:p-5 border-t-2 border-border">
        <livewire:component.user-setting />
    </footer>
</div>
