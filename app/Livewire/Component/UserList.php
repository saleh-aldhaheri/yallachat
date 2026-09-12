<?php

namespace App\Livewire\Component;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public ?Collection $users = null;

    public string $search = '';

    public int $page = 1;

    public bool $hasMorePages = true;

    public function mount(): void
    {
        $this->loadUsers();
    }

    public function updatedSearch(): void
    {
        $this->resetPaginationState();
        $this->loadUsers();
    }

    public function loadMore(): void
    {
        if (! $this->hasMorePages) {
            return;
        }

        $this->page++;
        $this->loadUsers();
    }

    private function resetPaginationState(): void
    {
        $this->page = 1;
        $this->users = collect([]);
        $this->hasMorePages = true;
    }

    private function loadUsers(): void
    {
        $myChatIds = auth()->user()->joinedChats()->pluck('chat_id');

        $query = User::query()
            ->where('id', '!=', auth()->id())
            ->where(function ($query) use ($myChatIds) {
                $query->whereDoesntHave('joinedChats')
                    ->orWhereDoesntHave('joinedChats', function ($subQuery) use ($myChatIds) {
                        $subQuery->whereIn('chat_id', $myChatIds);
                    });
            });

        if (! empty(trim($this->search))) {
            $query->where('name', 'LIKE', "%{$this->search}%");
        }

        $paginator = $query->paginate(20, ['*'], 'page', $this->page);

        $newUsers = collect($paginator->items());
        $this->users = $newUsers->merge($this->users);

        $this->hasMorePages = $paginator->hasMorePages();
    }

    #[On('refresh-user-list')]
    public function refreshList(User $user): void
    {
        $this->users = $this->users->reject(fn ($u) => $u->id == $user->id);
    }

    public function render(): View
    {
        return view('livewire.components.user-list');
    }
}
