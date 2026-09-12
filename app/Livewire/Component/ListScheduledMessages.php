<?php

namespace App\Livewire\Component;

use App\Models\ScheduledMessage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class ListScheduledMessages extends Component
{
    public int $page = 1;

    public int $limit = 10;

    public bool $hasMorePages = false;

    public Collection $scheduledMessages;

    public string $search = '';

    public ?string $activeFilter = null;

    public bool $isDeleteShow = false;

    public bool $isViewShow = false;

    public ?ScheduledMessage $scheduledMessage = null;

    public function mount(): void
    {
        $this->resetPaginationState();
        $this->loadScheduleMessages();
    }

    private function resetPaginationState(): void
    {
        $this->page = 1;
        $this->scheduledMessages = collect([]);
        $this->hasMorePages = true;
    }

    public function loadMore(): void
    {
        if (! $this->hasMorePages) {
            return;
        }

        $this->page++;
        $this->loadScheduleMessages();
    }

    public function updatedSearch(): void
    {
        $this->resetPaginationState();
        $this->loadScheduleMessages();
    }

    public function handleActiveFilter(?string $value = null): void
    {
        $this->activeFilter = $value;
        $this->resetPaginationState();
        $this->loadScheduleMessages();
    }

    public function loadScheduleMessages(): void
    {
        $query = auth()->user()->ScheduledMessages();

        if ($this->activeFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->activeFilter === 'inactive') {
            $query->where('is_active', false);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('topic', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        $paginator = $query->withCount('chats')->orderByDesc('created_at')
            ->paginate(20, ['*'], 'page', $this->page);

        $this->scheduledMessages = $this->scheduledMessages->merge($paginator->items());

        $this->hasMorePages = $paginator->hasMorePages();
    }

    public function toggleActive(int $id): void
    {
        $message = ScheduledMessage::findOrFail($id);
        $message->update(['is_active' => ! $message->is_active]);
        Toaster::success($message->is_active ? 'Scheduled message activated.' : 'Scheduled message deactivated.');
    }

    public function update(int $id): void
    {
        $message = ScheduledMessage::findOrFail($id);
        $this->dispatch('set-scheduled-message', $message);
        $this->dispatch('set-page', 'update_scheduled_message');
    }

    public function broadcast(int $id): void
    {
        $message = ScheduledMessage::findOrFail($id);
        $this->dispatch('set-scheduled-message', $message);
        $this->dispatch('set-page', 'manage_broadcasts');
    }

    public function view(int $id): void
    {
        $this->scheduledMessage = ScheduledMessage::with('chats')->findOrFail($id);
        $this->isViewShow = true;
    }

    public function closeView(): void
    {
        $this->isViewShow = false;
        $this->scheduledMessage = null;
    }

    public function showDelete(int $id): void
    {
        $this->scheduledMessage = ScheduledMessage::findOrFail($id);
        $this->isDeleteShow = true;
    }

    public function closeDelete(): void
    {
        $this->isDeleteShow = false;
        $this->scheduledMessage = null;
    }

    public function delete(): void
    {
        if (! $this->scheduledMessage) {
            return;
        }

        $this->scheduledMessage->delete();
        $this->scheduledMessage = null;
        $this->isDeleteShow = false;
        $this->resetPaginationState();
        $this->loadScheduleMessages();
        Toaster::success('Scheduled message deleted.');
    }

    public function chatNames(ScheduledMessage $scheduledMessage): string
    {
        return $scheduledMessage->chats->map(function ($chat) {
            $name = $chat->name;

            if ($chat->type === 'private') {
                $name = $chat->participants()
                    ->whereNot('participant_id', auth()->id())
                    ->first()?->name ?? $chat->name;
            }

            return $name;
        })->implode(', ');
    }

    public function scheduleSummary(ScheduledMessage $scheduledMessage): string
    {
        if ($scheduledMessage->schedule_type === 'specific_dates') {
            return implode(', ', $scheduledMessage->run_dates ?? []);
        }

        $summary = ucfirst($scheduledMessage->frequency ?? 'daily');

        if (($scheduledMessage->frequency ?? '') === 'monthly' && $scheduledMessage->start_date) {
            $start = Carbon::parse($scheduledMessage->start_date);
            $summary .= ' · '.$this->nthLabel(intdiv($start->day - 1, 7) + 1)
                .' '.($this->weekdays()[$start->dayOfWeek] ?? '');
        }

        return $summary;
    }

    private function nthLabel(int $nth): string
    {
        return match ($nth) {
            -1 => 'last',
            1 => 'first',
            2 => 'second',
            3 => 'third',
            4 => 'fourth',
            5 => 'fifth',
            default => '',
        };
    }

    private function weekdays(): array
    {
        return [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
    }

    public function render()
    {
        return view('livewire.components.list-scheduled-messages');
    }
}
