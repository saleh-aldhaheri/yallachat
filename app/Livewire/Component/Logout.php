<?php

namespace App\Livewire\Component;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class Logout extends Component
{
    public function render(): View
    {
        return view('livewire.components.logout');
    }

    public function logout(): mixed
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
