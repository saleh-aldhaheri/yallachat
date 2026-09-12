<?php

namespace App\Livewire\Component;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class AddUserGroup extends Component
{
    public User $user;

    public bool $isRemovable = false;

    public function render(): View
    {
        return view('livewire.components.adduser-group');
    }

    public function addUserGroup(): void
    {
        $this->dispatch('add-user-to-group', $this->user);
    }

    public function removeUserGroup(): void
    {
        $this->dispatch('remove-user-from-group', $this->user);
    }
}
