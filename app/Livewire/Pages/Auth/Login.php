<?php

namespace App\Livewire\Pages\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Login extends Component
{
    #[Validate('required|email')]
    public string $email;

    #[Validate('required|min:8|max:20')]
    public string $password;

    public function render()
    {
        return view('livewire.pages.auth.login');
    }

    public function save(): void
    {
        $this->validate();

        if (Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ])) {
            session()->regenerate();
            Toaster::success('Welcome back!');
            $this->redirect('chat');

            return;
        }

        Toaster::error('Wrong email or password.');
        $this->addError('email', 'wrong email or password');
    }
}
