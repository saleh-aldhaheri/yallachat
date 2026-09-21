<?php

namespace App\Livewire\Component;

use App\Models\NotificationChannel;
use App\Services\WhatsappChannelConfigurationService;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;
use Propaganistas\LaravelPhone\Rules\Phone;

class WhatsappChannelConfiguration extends Component
{
    public ?NotificationChannel $notificationChannel = null;

    public bool $showModal = false;

    #[Validate([
        'required',
        new Phone(type: 'mobile'),
        'regex:/^\+[1-9]\d{6,14}$/',
    ])]
    public string $phoneNumber = '';

    public string $code = '';

    public bool $codeSent = false;

    public bool $reconnect = false;

    public function render(): View
    {
        return view('livewire.components.whatsapp-channel-configuration');
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
        $this->phoneNumber = $this->notificationChannel?->configuration['phone_number'] ?? '';
        $this->showModal = true;
    }

    public function remove(): void
    {
        $this->notificationChannel?->delete();

        $this->notificationChannel = null;

        Toaster::success('WhatsApp channel removed.');

        $this->dispatch('notification-channels-updated');
    }

    public function toggleActive(): void
    {
        $channel = $this->notificationChannel;

        if (! $channel) {
            return;
        }

        $channel->update(['is_active' => ! $channel->is_active]);

        Toaster::success($channel->is_active ? 'WhatsApp notifications enabled.' : 'WhatsApp notifications disabled.');

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
        $this->validate(['phoneNumber' => ['required', 'regex:/^\+?[0-9]{8,15}$/']]);

        if (! $this->service()->send($this->phoneNumber, auth()->user())) {
            $this->addError('phoneNumber', 'Too many requests. Please wait a moment before requesting another code.');
            Toaster::error('Too many requests. Please wait a moment before requesting another code.');

            return;
        }

        $this->codeSent = true;
        $this->code = '';
    }

    public function submitVerify(): void
    {
        $this->validate(['code' => ['required', 'digits:6']]);

        if (! $this->service()->verify($this->phoneNumber, $this->code)) {
            $this->addError('code', 'Invalid or expired verification code.');
            Toaster::error('Invalid or expired verification code.');

            return;
        }

        $this->notificationChannel = $this->service()->persist(auth()->user(), $this->phoneNumber);

        $this->resetFlow();

        Toaster::success('WhatsApp connected successfully.');

        $this->dispatch('notification-channels-updated');
    }

    public function cancel(): void
    {
        $this->resetFlow();
    }

    private function service(): WhatsappChannelConfigurationService
    {
        return app(WhatsappChannelConfigurationService::class);
    }

    private function resetFlow(): void
    {
        $this->showModal = false;
        $this->phoneNumber = '';
        $this->code = '';
        $this->codeSent = false;
        $this->reconnect = false;
    }
}
