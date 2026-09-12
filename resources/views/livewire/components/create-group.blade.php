<div class="h-full flex flex-col min-h-0">
    <x-search-input variant="underline" wire:model.live.debounce.300ms="search" placeholder="Search..."/>

    <section class="h-full min-h-0 pt-5">
        <form wire:submit.prevent="createGroup" class="h-full min-h-0 space-y-5 px-5 py-2 grid grid-rows-[auto_1fr_50px]">
            <div>
                <x-flash-message class="mb-6" :timeout="1000"/>

                <x-form-error name="users"/>

                <x-avatar-upload target="groupAvatar" error="groupAvatar"
                                 :preview-url="$groupAvatar && method_exists($groupAvatar, 'isPreviewable') && $groupAvatar->isPreviewable() ? $groupAvatar->temporaryUrl() : null"
                                 :initials="strtoupper(substr($name ?: 'G', 0, 2))"/>

                <x-form-field label="group name" name="name">
                    <x-form-input wire:model="name" placeholder="group name"/>
                </x-form-field>
            </div>

            <div class="flex-1 overflow-y-auto no-scrollbar min-h-0">
                <div class="flex justify-between items-center gap-2">
                    <h5 class="text-them truncate min-w-0">~{{config('app.name')}}/Group/{{$userListType}}</h5>
                    <x-actions-menu>
                        <x-btn-primary wire:click="setUserListType('unselected')">User List</x-btn-primary>
                        <x-btn-primary wire:click="setUserListType('selected')">Group Users</x-btn-primary>
                    </x-actions-menu>
                </div>

                @if($userListType === 'unselected')
                    @foreach($usersList as $user)
                        <livewire:component.add-user-group :user="$user" wire:key="user-{{ $user->id }}" :is-removable="false" />
                    @endforeach
                @else
                    @foreach($users as $user)
                        @if($user->id !== auth()->id())
                            <livewire:component.add-user-group :user="$user" wire:key="user-{{ $user->id }}" :is-removable="true" />
                        @endif
                    @endforeach
                @endif

                <x-loading-more :show="$hasMorePages" label="Loading more users..."/>
            </div>

            <div>
                <x-submit-button class="w-full py-2.5">create group</x-submit-button>
            </div>
        </form>
    </section>
</div>
