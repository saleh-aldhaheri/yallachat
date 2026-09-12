<div class="h-full min-h-0 grid grid-rows-[1fr_auto]">
    <div
        class="min-h-0 overflow-y-auto no-scrollbar"
        x-data="{
                ready: false,
                scrollToBottom(smooth = false) {
                    this.$nextTick(() => {
                        this.$el.scrollTo({
                            top: this.$el.scrollHeight,
                            behavior: smooth ? 'smooth' : 'auto'
                        });
                    });
                }
            }"
        x-init="
                setTimeout(() => {
                    scrollToBottom(false);

                    setTimeout(() => { ready = true }, 100);
                }, 50);
            "
        @scroll-to-bottom.window="scrollToBottom(true)"
    >

        <x-loading-more :show="$hasMorePages" label="Loading older messages..." x-show="ready"/>

        @foreach($messages as $message)
            <div class="p-3 sm:p-5">
                <x-message
                    :message="$message"
                    :is-mine="$message->sender_id === auth()->id()"
                />
            </div>
        @endforeach
    </div>

    <div
        class="p-3 sm:p-5 relative"
        x-data="{
            showEmojiPicker: false,
            pickerWidth: 336,
            pickerHeight: 400,
            pickerPos: { top: 0, left: 0 },
            toggleEmojiPicker() {
                this.showEmojiPicker = !this.showEmojiPicker;
                if (this.showEmojiPicker) {
                    this.positionPicker();
                    this.$nextTick(() => this.initPicker());
                }
            },
            positionPicker() {
                const btn = this.$refs.emojiButton.getBoundingClientRect();
                const margin = 8;

                let left = btn.right - this.pickerWidth;
                let top = btn.top - this.pickerHeight - margin;

                left = Math.max(margin, Math.min(left, window.innerWidth - this.pickerWidth - margin));

                if (top < margin) {
                    top = btn.bottom + margin;
                }
                top = Math.max(margin, Math.min(top, window.innerHeight - this.pickerHeight - margin));

                this.pickerPos = { top, left };
            },
            destroyPicker() {
                const container = this.$refs.pickerContainer;
                if (container && container._picmoInstance) {
                    container._picmoInstance.destroy?.();
                    container._picmoInstance = null;
                }
            },
            initPicker() {
                const container = this.$refs.pickerContainer;
                if (!container) return;

                // picmo's virtualized grid goes blank when re-shown after being hidden,
                // so we destroy and recreate the instance on every open.
                this.destroyPicker();

                container._picmoInstance = picmo.createPicker({
                    rootElement: container,
                    theme: 'dark',
                    emojisPerRow: 8,
                    emojiSize: '1.6rem',
                });

                container._picmoInstance.addEventListener('emoji:select', selection => {
                    const textarea = this.$refs.inputArea;
                    const start = textarea.selectionStart || 0;
                    const end = textarea.selectionEnd || 0;

                    const text = textarea.value;
                    const newValue = text.slice(0, start) + selection.emoji + text.slice(end);

                    textarea.value = newValue;

                    this.$wire.set('message', newValue, false);

                    this.$nextTick(() => {
                        textarea.focus();
                        const newCursorPos = start + selection.emoji.length;
                        textarea.setSelectionRange(newCursorPos, newCursorPos);

                        textarea.style.height = 'auto';
                        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
                    });

                    this.showEmojiPicker = false;
                });
            }
        }"
        x-init="
            () => {
                this.$watch('showEmojiPicker', open => {
                    if (!open) {
                        this.$nextTick(() => this.destroyPicker());
                    }
                });
            }
        "
        x-on:resize.window="if (showEmojiPicker) positionPicker()"
    >
        <template x-teleport="body">
            <div
                x-show="showEmojiPicker"
                x-cloak
                x-on:click.outside="if ($event.target !== $refs.emojiButton) showEmojiPicker = false"
                x-ref="pickerContainer"
                x-bind:style="`position: fixed; top: ${pickerPos.top}px; left: ${pickerPos.left}px; width: ${pickerWidth}px; height: ${pickerHeight}px;`"
                class="z-[9999] bg-panel border-2 border-border rounded-xl overflow-hidden shadow-2xl"
            ></div>
        </template>

        <form
            wire:submit.prevent="send"
            class="flex items-end gap-3"
        >
            <div class="relative w-full">
                <textarea
                    wire:model="message"
                    x-ref="inputArea"
                    rows="1"
                    x-on:input="
                        $el.style.height = 'auto';
                        $el.style.height = Math.min($el.scrollHeight, 120) + 'px';
                    "
                    x-on:keydown.enter="
                        if (!$event.shiftKey) {
                            $event.preventDefault();
                            $wire.send().then(() => { $dispatch('scroll-to-bottom') });
                        }
                    "
                    class="w-full py-2 pl-2 pr-10 resize-none overflow-hidden border-b-2 border-b-ink-muted focus:outline-none focus:ring-0 focus:border-b-them"
                    placeholder="...send"
                ></textarea>

                <button
                    type="button"
                    x-ref="emojiButton"
                    x-on:click="toggleEmojiPicker()"
                    class="absolute right-2 bottom-2 text-ink-muted hover:text-them transition focus:outline-none cursor-pointer"
                    aria-label="emoji picker"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="9" stroke-linecap="round"/>
                        <circle cx="9" cy="10" r="0.75" fill="currentColor"/>
                        <circle cx="15" cy="10" r="0.75" fill="currentColor"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 14.5s1.2 1.8 3.5 1.8 3.5-1.8 3.5-1.8"/>
                    </svg>
                </button>
            </div>

            <button type="submit" class="btn-primary shrink-0">Send</button>
        </form>
    </div>

</div>
