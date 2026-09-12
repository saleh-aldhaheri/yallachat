<?php

namespace App\Livewire\Component;

use App\Models\NotificationChannel;
use App\Services\EmailChannelConfigurationService;
use Illuminate\View\View;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class EmailChannelConfiguration extends Component
{
    public ?NotificationChannel $notificationChannel = null;

    public bool $showModal = false;

    public string $email = '';

    public string $code = '';

    public bool $codeSent = false;

    public bool $reconnect = false;

    public function render(): View
    {
        return view('livewire.components.email-channel-configuration');
    }

    public function configure(): void
    {
        $this->resetFlow();
        $this->showModal = true;
    }

    public function reconnect(): void
    {
        $this->resetFlow();
        $this->reconnect = true;
        $this->email = $this->notificationChannel?->configuration['address'] ?? '';
        $this->showModal = true;
    }

    public function remove(): void
    {
        $this->notificationChannel?->delete();

        $this->notificationChannel = null;

        Toaster::success('Email channel removed.');

        $this->dispatch('notification-channels-updated');
    }

    public function toggleActive(): void
    {
        $channel = $this->notificationChannel;

        if (! $channel) {
            return;
        }

        $channel->update(['is_active' => ! $channel->is_active]);

        Toaster::success($channel->is_active ? 'Email notifications enabled.' : 'Email notifications disabled.');

        $this->dispatch('notification-channels-updated');
    }

    public function submit(): void
    {
        if (! $this->codeSent) {
            $this->sendCode();
        } else {
            $this->submitVerify();
        }
    }

    public function sendCode(): void
    {
        $this->validate(['email' => ['required', 'email']]);

        if (! (new EmailChannelConfigurationService)->sendCode(auth()->user(), $this->email)) {
            $this->addError('email', 'Too many requests. Please wait a moment before requesting another code.');
            Toaster::error('Too many requests. Please wait a moment before requesting another code.');

            return;
        }

        $this->codeSent = true;
        $this->code = '';
    }

    public function submitVerify(): void
    {
        $this->validate(['code' => ['required', 'digits:6']]);

        $service = new EmailChannelConfigurationService;

        if (! $service->verify($this->email, $this->code)) {
            $this->addError('code', 'Invalid or expired verification code.');
            Toaster::error('Invalid or expired verification code.');

            return;
        }

        $this->notificationChannel = $service->persist(auth()->user(), $this->email);

        $this->resetFlow();

        Toaster::success('Email connected successfully.');

        $this->dispatch('notification-channels-updated');
    }

    public function cancel(): void
    {
        $this->resetFlow();
    }

    private function resetFlow(): void
    {
        $this->showModal = false;
        $this->email = '';
        $this->code = '';
        $this->codeSent = false;
        $this->reconnect = false;
    }
}
