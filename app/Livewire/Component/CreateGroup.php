<?php

namespace App\Livewire\Component;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class CreateGroup extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Validate('nullable|image|max:2048')]
    public $groupAvatar;

    #[Validate('required|min:2|unique:chats,name')]
    public string $name;

    public Collection $users;

    public ?Collection $usersList = null;

    public int $page = 1;

    public string $search = '';

    public bool $hasMorePages = false;

    public string $userListType = 'unselected';

    public function render(): View
    {
        return view('livewire.components.create-group');
    }

    public function mount(): void
    {
        $this->users = collect([]);
        $this->users->push(auth()->user());
        $this->loadUsers();
    }

    #[On('add-user-to-group')]
    public function addUser(User $user): void
    {
        if ($this->users->contains(fn ($u) => $u->id === $user->id)) {
            return;
        }
        $this->users->push($user);
        $this->usersList = $this->usersList->reject(fn ($u) => $u->id === $user->id);
    }

    #[On('remove-user-from-group')]
    public function removeUser(User $user): void
    {
        $this->users = $this->users->reject(fn ($u) => $u->id === $user->id);
        $this->usersList->push($user);
    }

    public function createGroup(): void
    {
        $this->validate();
        if ($this->users->count() < 2) {
            $this->addError('users', 'Please select at least 2 participants.');
            Toaster::error('Please select at least 2 participants.');

            return;
        }
        DB::transaction(function () {
            $chat = Chat::create([
                'name' => $this->name,
                'created_by' => auth()->user()->id,
                'type' => 'group',
            ]);

            $chat->participants()->attach($this->users->pluck('id')->toArray());

            if ($this->groupAvatar) {
                $chat->addMedia($this->groupAvatar)
                    ->sanitizingFileName(fn (string $fileName): string => preg_replace('/[^\pL\pN._-]+/u', '-', $fileName))
                    ->toMediaCollection('group-avatar');
            }

            Toaster::success("Group {$this->name} created successfully.");

            $this->mount();
        });
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
        $this->usersList = collect([]);
        $this->hasMorePages = true;
    }

    private function loadUsers(): void
    {
        $query = User::query()->whereNotIn('id', $this->users->pluck('id')->all());
        if (! empty(trim($this->search))) {
            $query->where('name', 'LIKE', "%{$this->search}%");
        }
        $paginator = $query->paginate(20, ['*'], 'page', $this->page);
        $newUsers = collect($paginator->items());
        $this->usersList = $newUsers->merge($this->usersList);
        $this->hasMorePages = $paginator->hasMorePages();
    }

    public function setUserListType(string $type): void
    {
        $this->userListType = $type;
    }
}
