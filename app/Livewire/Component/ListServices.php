<?php

namespace App\Livewire\Component;

use App\Enums\ServicesEnum;
use App\Models\Service;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class ListServices extends Component
{
    public function render()
    {
        return view('livewire.components.list-services',
            [
                'userServices' => auth()->user()->services(),
            ]
        );
    }

    public function configure(string $service): mixed
    {
        $service = ServicesEnum::TryFrom($service);

        return redirect()->route('oauth.redirect', ['provider' => $service->providers(), 'service' => $service->scopes()]);
    }

    public function remove(Service $service): void
    {
        $service->delete();
        Toaster::success('Service deleted successfully.');
    }

    public function toggleActive(Service $service): void
    {
        $service->update([
            'is_active' => ! $service->is_active,
        ]);

        Toaster::success($service->is_active ? 'Service enabled.' : 'Service disabled.');
    }
}
