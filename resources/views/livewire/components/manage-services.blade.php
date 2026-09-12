<div class="grid grid-rows-[auto_1fr] lg:grid-rows-[80px_1fr] grid-cols-[minmax(0,1fr)] px-4 sm:px-10">
    <x-panel-header title="Services">
        <x-btn-primary class="lg:hidden" wire:click="$dispatch('go-back')">←</x-btn-primary>
    </x-panel-header>
    <section class="p-3 sm:p-5 min-w-0">
        <x-command-header command="services" flag="--manage"
                          description="Connect third-party tools the agent can use to understand your context and give better, more accurate Messages."/>
        <livewire:component.list-services />
    </section>
</div>
