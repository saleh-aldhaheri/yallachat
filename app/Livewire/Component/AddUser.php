<?php

namespace App\Livewire\Component;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class AddUser extends Component
{
    public User $user;

    public function render(): View
    {
        return view('livewire.components.add-user');
    }

    public function addUser(): void
    {
        DB::transaction(function () {
            $chat = Chat::create([
                'created_by' => auth()->user()->id,
                'type' => 'private',
                'name' => auth()->user()->name.'_'.$this->user->name,
            ]);

            $chat->participants()->attach(auth()->user()->id);
            $chat->participants()->attach($this->user->id);

            $this->dispatch('refresh-user-list', $this->user);
        });

        Toaster::success("Chat started with {$this->user->name}.");
    }
}
