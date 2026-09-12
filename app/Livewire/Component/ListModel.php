<?php

namespace App\Livewire\Component;

use App\Ai\Agents\ChatAgent;
use App\Ai\Agents\CheckConnectionAgent;
use App\Models\AiModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class ListModel extends Component
{
    public bool $isDeleteShow = false;

    public bool $isViewShow = false;

    public ?AiModel $model = null;

    public function render()
    {
        return view('livewire.components.list-model', [
            'models' => auth()->user()->aiModels()->get(),
        ]);
    }

    public function view(int $id): void
    {
        $this->model = AiModel::findOrFail($id);
        $this->isViewShow = true;
    }

    public function closeView(): void
    {
        $this->isViewShow = false;
        $this->model = null;
    }

    public function update(int $id): void
    {
        $model = AiModel::findOrFail($id);
        $this->dispatch('set-model', $model);
        $this->dispatch('set-page', 'update_model');
    }

    public function showDelete(int $id): void
    {
        $this->model = AiModel::findOrFail($id);
        $this->isDeleteShow = true;
    }

    public function closeDelete(): void
    {
        $this->isDeleteShow = false;
        $this->model = null;
    }

    public function delete(): void
    {
        if (! $this->model) {
            return;
        }
        $this->model->delete();
        $this->model = null;
        $this->isDeleteShow = false;
        Toaster::success('AI model deleted.');
    }

    public function toggleActive(int $id): void
    {
        $model = AiModel::findOrFail($id);
        $newState = ! $model->is_active;
        if ($newState) {
            $model->user->aiModels()->where('id', '!=', $id)->update(['is_active' => false]);
        }
        $model->update(['is_active' => $newState]);
        Toaster::success($newState ? 'Model activated.' : 'Model deactivated.');
    }

    public function connect(int $id): void
    {
        $model = AiModel::findOrFail($id);

        try {
            (new CheckConnectionAgent($model->api_key))->prompt('testing', model: $model->name->value);
            $model->update(['is_available' => true]);
            Toaster::success('Model connected successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            Toaster::error('Could not connect to the model.');
        }
    }
}
