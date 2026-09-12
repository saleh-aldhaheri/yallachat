<?php

namespace App\Livewire\Component;

use App\Ai\Agents\CheckConnectionAgent;
use App\Enums\ModelsEnum;
use App\Models\AiModel;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class HandleModel extends Component
{
    public ?AiModel $aiModel = null;

    #[Validate('required|string')]
    public string $name;

    #[Validate('required|string')]
    public string $apiKey;

    #[Validate('nullable|string|max:255')]
    public ?string $persona = null;

    #[Validate('nullable|string|max:255')]
    public ?string $tone = null;

    public bool $isMultiLanguage = false;

    public bool $isAutoLanguage = false;

    public function mount(): void
    {
        if ($this->aiModel) {
            $this->name = $this->aiModel->name->value;
            $this->apiKey = $this->aiModel->api_key;
            $this->persona = $this->aiModel->persona;
            $this->tone = $this->aiModel->tone;
            $this->isMultiLanguage = $this->aiModel->is_multi_language;
            $this->isAutoLanguage = $this->aiModel->is_auto_language;
        }
    }

    public function render()
    {
        return view('livewire.components.handle-model', [
            'models' => ModelsEnum::cases(),
        ]);
    }

    public function submit(): void
    {
        $this->validate();

        if (! ModelsEnum::tryFrom($this->name)) {
            $this->addError('name', "model {$this->name} is not a valid model type");
            Toaster::error("{$this->name} is not a valid model type.");

            return;
        }

        $existing = AiModel::where('name', $this->name)
            ->where('user_id', auth()->user()->id)
            ->first();

        $modelAvailability = $this->checkModelAvailability($this->name, $this->apiKey);

        AiModel::updateOrCreate(
            [
                'name' => $this->name,
                'user_id' => auth()->user()->id,
            ],
            [
                'api_key' => $this->apiKey,
                'persona' => $this->persona,
                'tone' => $this->tone,
                'is_multi_language' => $this->isMultiLanguage,
                'is_auto_language' => $this->isAutoLanguage,
                'is_active' => $existing ? $existing->is_active : false,
                'is_available' => $modelAvailability,
            ]);

        if (! $modelAvailability) {
            Toaster::warning('Model saved, but the connection check failed.');
        } else {
            Toaster::success('AI model saved successfully.');
        }

        $this->reset();
        $this->dispatch('set-page', 'list_model');
    }

    public function checkModelAvailability(string $modelName, string $apiKey): bool
    {
        try {
            (new CheckConnectionAgent($apiKey))
                ->prompt('check connection', model: $modelName);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return false;
        }

        return true;
    }
}
