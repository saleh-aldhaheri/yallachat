<?php

namespace App\Livewire\Component;

use App\Models\AiModel;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageAiModel extends Component
{
    public string $page = 'list_model';

    public ?AiModel $aiModel = null;

    public function render()
    {
        return view('livewire.components.manage-ai-model');
    }

    #[On('set-page')]
    public function setPage(string $page)
    {
        if ($page !== 'update_model') {
            $this->aiModel = null;
        }

        $this->page = $page;
    }

    #[On('set-model')]
    public function setModel(AiModel $aiModel): void
    {
        $this->aiModel = $aiModel;
    }
}
