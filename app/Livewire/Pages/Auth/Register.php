<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Register extends Component
{
    #[Validate('required|min:2|regex:/^[\pL\s\-]+$/u')]
    public string $name;

    #[Validate('required|unique:users,email|email')]
    public string $email;

    #[Validate('required|min:8|max:20|confirmed')]
    public string $password;

    #[Validate('required')]
    public string $password_confirmation = '';

    public ?string $gender = null;

    public function render(): View
    {
        return view('livewire.pages.auth.register');
    }

    public function save(): mixed
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'gender' => $this->gender,
        ]);

        Toaster::success('Registration successful! Please log in.');

        return $this->redirect(route('login'), navigate: true);
    }

    public function setGender(string $gender): void
    {
        if ($gender !== 'm' && $gender !== 'f') {
            return;
        }

        $this->gender = $gender;
    }
}
