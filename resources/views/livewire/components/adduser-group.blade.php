<x-user-row :user="$user">
    @if($isRemovable)
        <x-btn-primary wire:click="removeUserGroup" class="border-danger text-danger">Remove</x-btn-primary>
    @else
        <x-btn-primary wire:click="addUserGroup">Add</x-btn-primary>
    @endif
</x-user-row>
