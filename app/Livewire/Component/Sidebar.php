<?php

namespace App\Livewire\Component;

use Illuminate\View\View;
use Livewire\Component;

class Sidebar extends Component
{
    public $panel = 'chat';

    public function setPanel(string $panel): void
    {
        $this->panel = $panel;
    }

    public function render(): View
    {
        return view('livewire.components.sidebar');
    }
}
