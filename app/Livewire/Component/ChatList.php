<?php

namespace App\Livewire\Component;

use App\Models\Chat;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class ChatList extends Component
{
    use WithPagination;

    public ?Collection $chats = null;

    public int $page = 1;

    public string $search = '';

    public bool $hasMorePages = true;

    public function mount(): void
    {
        $this->loadChats();
    }

    public function render(): View
    {
        return view('livewire.components.chat-list');
    }

    public function loadChats(): void
    {

        $query = auth()->user()->joinedChats();

        if (! empty(trim($this->search))) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        $paginator = $query->paginate(20, ['*'], 'page', $this->page);

        $newChats = collect($paginator->items());

        $this->chats = $newChats->merge($this->chats);

        $this->hasMorePages = $paginator->hasMorePages();
    }

    public function loadMore(): void
    {
        if (! $this->hasMorePages) {
            return;
        }

        $this->page++;
        $this->loadChats();
    }

    private function resetPaginationState(): void
    {
        $this->page = 1;
        $this->chats = collect([]);
        $this->hasMorePages = true;
    }

    public function updatedSearch(): void
    {
        $this->resetPaginationState();
        $this->loadChats();
    }

    #[On('refresh-chat-list')]
    public function refreshList(): void
    {
        $this->resetPaginationState();
        $this->loadChats();
    }

    #[On('remove-chat')]
    public function removeChat(int $chatId): void
    {
        $this->dispatch('chat-close');

        $chat = Chat::query()->find($chatId);

        if (! $chat) {
            $this->chats = $this->chats->reject(fn ($c) => $c->id === $chatId);

            return;
        }

        $isParticipant = $chat->participants()
            ->where('participant_id', auth()->id())
            ->exists();

        if (! $isParticipant) {
            $this->chats = $this->chats->reject(fn ($c) => $c->id === $chatId);

            return;
        }

        if ($chat->type === 'group' && $chat->created_by !== auth()->id()) {
            $chat->participants()->detach(auth()->id());
        } else {
            $chat->delete();
        }

        Toaster::success('chat deleted!');
        $this->chats = $this->chats->reject(fn ($c) => $c->id === $chatId);
    }
}
