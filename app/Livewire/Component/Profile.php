<?php

namespace App\Livewire\Component;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

class Profile extends Component
{
    use WithFileUploads;

    #[Validate('nullable|image|max:2048')]
    public $avatar;

    #[Validate('sometimes|nullable|min:2')]
    public string $name = '';

    #[Validate('sometimes|nullable|email')]
    public string $email = '';

    #[Validate('sometimes|nullable|min:8|max:20')]
    public string $oldPassword = '';

    #[Validate('sometimes|nullable|min:8|max:20|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    #[Validate('nullable|string|min:1|max:1|in:m,f')]
    public string $gender;

    public function mount(): void
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
        $this->gender = auth()->user()?->gender ?? '';
    }

    public function render(): View
    {
        return view('livewire.components.profile');
    }

    public function closeProfile(): void
    {
        $this->dispatch('service-open');
    }

    public function updateProfile(): void
    {
        if ($this->avatar && method_exists($this->avatar, 'exists') && ! $this->avatar->exists()) {
            $this->avatar = null;
        }

        $this->validate();

        if ($this->email) {
            $this->validate([
                'email' => Rule::unique('users', 'email')->ignore(auth()->id()),
            ]);
        }

        $user = auth()->user();

        if ($this->name) {
            $user->name = $this->name;
        }

        if ($this->email) {
            $user->email = $this->email;
        }

        if ($this->gender) {
            $user->gender = $this->gender;
        }

        if ($this->oldPassword) {
            if (Hash::check($this->oldPassword, $user->password)) {
                if ($this->password) {
                    $user->password = Hash::make($this->password);
                } else {
                    $this->addError('password', 'Password should not be empty.');
                    Toaster::error('Password should not be empty.');

                    return;
                }
            } else {
                $this->addError('oldPassword', 'Old password is incorrect.');
                Toaster::error('Old password is incorrect.');

                return;
            }
        }

        $userChanged = false;

        if ($this->avatar) {
            $user->addMedia($this->avatar)
                ->sanitizingFileName(fn (string $fileName): string => preg_replace('/[^\pL\pN._-]+/u', '-', $fileName))
                ->toMediaCollection('avatar');
            $userChanged = true;
        }

        if ($user->isDirty()) {
            $user->save();
            $userChanged = true;
        }

        if ($userChanged) {
            Toaster::success('Profile updated.');
        }
    }

    public function setGender(string $gender): void
    {
        if ($gender !== 'm' && $gender !== 'f') {
            return;
        }

        $this->gender = $gender;
    }
}
