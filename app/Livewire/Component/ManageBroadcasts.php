<?php

namespace App\Livewire\Component;

use App\Enums\ScheduledMessageStatusEnum;
use App\Jobs\SendScheduledMessagesJob;
use App\Models\ChatScheduledMessage;
use App\Models\ScheduledMessage;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class ManageBroadcasts extends Component
{
    use WithPagination;

    public ScheduledMessage $scheduledMessage;

    public string $search = '';

    public ?string $filter = null;

    public int $perPage = 10;

    public ?ChatScheduledMessage $viewing = null;

    public ?int $sendingId = null;

    public function view(int $id): void
    {
        $this->viewing = ChatScheduledMessage::with('chat')->findOrFail($id);
    }

    public function closeView(): void
    {
        $this->viewing = null;
    }

    public function pause(int $id): void
    {
        $chatScheduledMessage = ChatScheduledMessage::with('chat')->findOrFail($id);
        $chatScheduledMessage->is_paused = ! $chatScheduledMessage->is_paused;
        $chatScheduledMessage->save();
        $status = $chatScheduledMessage->is_paused ? 'paused' : 'resumed';
        Toaster::success("Message for {$chatScheduledMessage->chat->name} has been {$status}.");
    }

    public function send(int $id): void
    {
        $chatScheduledMessage = ChatScheduledMessage::find($id);

        $this->sendingId = $id;

        SendScheduledMessagesJob::dispatch($id);

        $chatName = $chatScheduledMessage?->chat?->name ?? 'chat';

        Toaster::success("Message sent to {$chatName}.");
    }

    public function chatInitials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name ?: '?'));
        $initials = mb_substr($words[0] ?? '', 0, 1);

        if (isset($words[1])) {
            $initials .= mb_substr($words[1], 0, 1);
        }

        return strtoupper($initials);
    }

    public function chatName(ChatScheduledMessage $broadcast): string
    {
        $chat = $broadcast->chat;

        if (! $chat) {
            return 'private';
        }

        if ($chat->type === 'private') {
            return $chat->participants
                ->first(fn ($participant) => $participant->id !== auth()->id())?->name
                ?? $chat->name;
        }

        return $chat->name;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function chatScheduledMessages()
    {
        $query = $this->scheduledMessage->chatScheduledMessages()
            ->chaperone()
            ->with(['chat.participants']);

        if ($this->filter === 'paused') {
            $query->where('is_paused', true);
        } elseif ($this->filter) {
            $query->where('is_paused', false)
                ->where('status', $this->filter);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->whereHas('chat', function ($chat) {
                    $chat->where('name', 'like', '%'.$this->search.'%');
                });
            });
        }

        return $query->paginate($this->perPage);
    }

    public function getListeners(): array
    {
        $authId = auth()->id();

        return [
            "echo-private:scheduled_message.{$authId},.App\Events\ScheduledMessageStatusChanged" => 'statusChanged',
        ];
    }

    public function statusChanged(array $event): void
    {
        if ($this->viewing !== null) {
            $this->viewing = ChatScheduledMessage::with('chat')->find($this->viewing->id);
        }
    }

    public function handleFilter(?string $value = null): void
    {
        $this->filter = $value;
        $this->resetPage();
    }

    public function render(): View
    {

        return view('livewire.components.manage-broadcasts', ['statuses' => ScheduledMessageStatusEnum::cases()]);
    }
}
