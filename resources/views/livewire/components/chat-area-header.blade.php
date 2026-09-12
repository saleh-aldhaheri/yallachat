<div class="flex border-b-2 items-center p-3 sm:p-5 border-border space-x-3 sm:space-x-4">
    <x-btn-primary class="lg:hidden shrink-0" wire:click="$dispatch('chat-close')">←</x-btn-primary>
    <x-avatar :image="$image" class="shrink-0"/>
    <div class="flex flex-col min-w-0">
        <h5 class="text-them truncate">~/{{$name}}</h5>
        @if($chatType == 'private')
            <p class="text-ink-muted truncate"> {{$participant->email}}  </p>
        @endif
    </div>

    @if($chatType === 'private')


        @if(!$isOnline && $isPriority)
            <div
                x-data="{showConfirm: false}"
                class="relative"
            >
                <x-btn-primary
                    x-on:click.stop="showConfirm = true"
                    class="text-danger">
                    Urgent Notification
                </x-btn-primary>

                <div
                    x-show="showConfirm"
                    x-cloak
                    x-on:click.outside="showConfirm = false"
                    x-on:keydown.escape.window="showConfirm = false"
                    class="absolute right-0 top-8 z-50 px-3 border border-them/40 bg-panel rounded-lg p-3 shadow-2xl"
                >
                    <form wire:submit.prevent="sendUrgent" class="w-72">
                        <x-form-head title="Urgent Message"></x-form-head>

                        <div class="px-4 py-4">
                            <x-form-field label="message" name="urgentMessage">
                                <x-form-textarea rows="6" maxlength="500" wire:model="urgentMessage" placeholder="Type the urgent message..." class="min-h-[120px]"></x-form-textarea>
                            </x-form-field>
                        </div>

                        <div class="flex justify-end gap-3 px-4 pb-3">
                            <button
                                type="button"
                                wire:click="cancelUrgent"
                                x-on:click.stop="showConfirm = false"
                                class="px-5 py-2 rounded-md text-xs font-mono font-semibold bg-danger text-page hover:brightness-110 transition-all cursor-pointer"
                            >
                                run ./cancel
                            </button>

                            <x-submit-button x-on:click.stop="showConfirm = false">run ./Send</x-submit-button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div class="text-xs text-ink-muted flex items-center gap-2 ml-auto shrink-0">
            <x-status-dot :active="$isOnline" size="md"/>
            {{$isOnline? "online" :  "offline"}}
        </div>

    @endif
</div>
