<div class="grid grid-rows-[auto_1fr] lg:grid-rows-[80px_1fr] grid-cols-[minmax(0,1fr)] px-4 sm:px-10">
    <x-panel-header title="AI-Models">
        <x-btn-primary class="lg:hidden" wire:click="$dispatch('go-back')">←</x-btn-primary>
        <x-slot:menu>
            @if($page !== 'list_model')
                <x-btn-primary wire:click="setPage('list_model')">
                    List Models
                </x-btn-primary>
            @endif
            @if($page !== 'add_model')
                <x-btn-primary wire:click="setPage('add_model')">
                    Add Model
                </x-btn-primary>
            @endif
        </x-slot:menu>
    </x-panel-header>

    <section class="p-3 sm:p-5 min-w-0">
        @if($page === "add_model")
            <x-command-header command="ai-model" flag="--add"
                              description="Register a new AI model and configure how it behaves in conversation."/>
            <livewire:component.handle-model />
        @elseif($page === "update_model" && $aiModel)
            <x-command-header command="ai-model" flag="--update" :arg="$aiModel->name->value"
                              description="Editing <span class='text-ink'>{{ $aiModel->name->value }}</span> Changes apply immediately on save."/>
            <livewire:component.handle-model :ai-model="$aiModel" />
        @else
            <x-command-header command="ai-model" flag="--list"
                              description="Models connected to this workspace. Pick the one that handles incoming chats — only one model can be active at a time."/>
            <livewire:component.list-model />
        @endif
    </section>
</div>
