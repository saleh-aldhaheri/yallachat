<div class="py-5 flex items-center gap-y-5 min-w-0" wire:click="openChat">
    <div class="shrink-0 mr-4">
        @if($chat->type === "private")
            <x-avatar :image="$participant?->avatar" />
        @else
            <x-avatar :image="$chat?->avatar"/>
        @endif
    </div>

    <div class="flex-1 min-w-0">
        <p class="font-semibold truncate">
            {{ $chat->type === 'private' ? $participant?->name : $chat->name }}
        </p>
        <p class="truncate">
            {{ $lastMessage ? \Illuminate\Support\Str::limit($lastMessage->message, 20, '...') : 'no message' }}
        </p>
    </div>

    <div class="shrink-0 flex flex-col items-center gap-2">
        @if($unreadCount > 0)
            <span class="bg-them text-ink text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
        <div>

            {{--  priority --}}
            @if(!$isPriority && $chat->type == 'private' && $hasNotificationChannels)
                <div
                    x-data="{ showConfirm: false }"
                    class="relative"
                >
                    <x-btn-primary
                        x-on:click.stop="showConfirm = true"
                        class="w-fit"
                    >
                        Mark priority
                    </x-btn-primary>
                    <div
                        x-show="showConfirm"
                        x-cloak
                        x-on:click.outside="showConfirm = false"
                        x-on:keydown.escape.window="showConfirm = false"
                        class="absolute right-0 top-8 z-50 w-48 border border-them/40 bg-panel rounded-lg p-3 shadow-2xl"
                    >
                        <p class="text-xs font-mono text-ink-muted mb-3 text-center">
                            Priority chats send you notifications through your configured channels while you're offline.
                        </p>
                        <div class="flex gap-2">
                            <x-btn-primary
                                x-on:click.stop="showConfirm = false"
                                class="text-ink-muted rounded py-1.5 hover:bg-panel/80 transition"
                            >
                                Cancel
                            </x-btn-primary>
                            <x-btn-primary
                                x-on:click.stop="$wire.updatePriority(); showConfirm = false"
                                class="rounded py-1.5 hover:bg-them/10 transition"
                            >
                                Confirm
                            </x-btn-primary>
                        </div>
                    </div>
                </div>
            @endif
            @if($isPriority && $chat->type == 'private' && $hasNotificationChannels)
                <div
                    x-data="{ showConfirm: false }"
                    class="relative"
                >
                    <x-btn-primary
                        x-on:click.stop="showConfirm = true"
                        class="w-fit"
                    >
                        Remove priority
                    </x-btn-primary>
                    <div
                        x-show="showConfirm"
                        x-cloak
                        x-on:click.outside="showConfirm = false"
                        x-on:keydown.escape.window="showConfirm = false"
                        class="absolute right-0 top-8 z-50 w-48 border border-them/40 bg-panel rounded-lg p-3 shadow-2xl"
                    >
                        <p class="text-xs font-mono text-ink-muted mb-3 text-center">
                            This will stop sending notifications for this chat while you're offline.
                        </p>
                        <div class="flex gap-2">
                            <x-btn-primary
                                x-on:click.stop="showConfirm = false"
                                class="text-ink-muted rounded py-1.5 hover:bg-panel/80 transition"
                            >
                                Cancel
                            </x-btn-primary>
                            <x-btn-primary
                                x-on:click.stop="$wire.updatePriority(); showConfirm = false"
                                class="rounded py-1.5 hover:bg-them/10 transition"
                            >
                                Confirm
                            </x-btn-primary>
                        </div>
                    </div>
                </div>
            @endif
            {{-- remove --}}
            <div
                x-data="{ showConfirm: false }"
                class="relative"
            >
                <x-btn-primary
                    x-on:click.stop="showConfirm = true"
                    class="border-danger text-danger cursor-pointer w-fit"
                >
                    Remove
                </x-btn-primary>
                <div
                    x-show="showConfirm"
                    x-cloak
                    x-on:click.outside="showConfirm = false"
                    x-on:keydown.escape.window="showConfirm = false"
                    class="absolute right-0 top-8 z-50 w-48 border border-danger/40 bg-panel rounded-lg p-3 shadow-2xl"
                >
                    <p class="text-xs font-mono text-ink-muted mb-3 text-center">
                        Remove this chat?
                    </p>
                    <div class="flex gap-2">
                        <x-btn-primary
                            x-on:click.stop="showConfirm = false"
                            class="text-ink-muted rounded py-1.5 hover:bg-panel/80 transition"
                        >
                            Cancel
                        </x-btn-primary>
                        <x-btn-primary
                            x-on:click.stop="$wire.removeChat(); showConfirm = false"
                            class="border-danger text-danger rounded py-1.5 hover:bg-danger/10 transition"
                        >
                            Remove
                        </x-btn-primary>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
