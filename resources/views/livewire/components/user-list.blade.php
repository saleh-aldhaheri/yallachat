<div class="h-full flex flex-col min-w-0 min-h-0">

    <x-search-input variant="underline" wire:model.live.debounce.300ms="search" placeholder="Search..."/>

    <div class="flex-1 overflow-y-auto no-scrollbar min-w-0 min-h-0">
        @foreach($users as $user)
            <livewire:component.add-user :user="$user" wire:key="user-{{ $user->id }}" />
        @endforeach

        <x-loading-more :show="$hasMorePages" label="Loading more users..."/>
    </div>
</div>
