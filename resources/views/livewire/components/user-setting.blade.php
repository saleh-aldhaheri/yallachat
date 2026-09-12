<div x-data="{ open: false }" class="relative">
    <div x-on:click="open = !open" class="text-them space-x-4 flex items-center cursor-pointer">
        <x-avatar :image="auth()->user()?->avatar" /> <span> ~/{{config('app.name')}}/Setting</span>
    </div>

    <template x-teleport="body">
        <div x-show="open" x-cloak
             x-on:click.outside="open = false"
             x-on:keydown.escape.window="open = false"
             class="fixed inset-0 z-50 flex justify-start">
            <div x-show="open"
                 x-transition.opacity.duration.200ms
                 x-on:click="open = false"
                 class="fixed inset-0 bg-black/50"></div>
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                  class="relative z-10 w-3/4 max-w-xs sm:w-fit h-full bg-page border-r-2 border-border p-4 sm:p-6 shadow-2xl overflow-y-auto flex flex-col">
                <div class="flex justify-between items-center mb-8">
                    <h5 class="text-them font-mono">~/{{config('app.name')}}/Setting</h5>
                    <x-btn-primary n x-on:click="open = false" class="text-ink-muted hover:text-them  text-lg leading-none">X</x-btn-primary>
                </div>
                <ul class="space-y-2 flex-1">
                    <li wire:click="openProfile; open = false" class="cursor-pointer font-mono text-ink-muted hover:text-them transition-colors px-3 py-2 rounded hover:bg-ink/5">~/{{config('app.name')}}/Profile</li>
                    <li wire:click="openAi" class="cursor-pointer font-mono text-ink-muted hover:text-them transition-colors px-3 py-2 rounded hover:bg-ink/5">~/{{config('app.name')}}/AI-Models</li>
                    <li wire:click="openService" class="cursor-pointer font-mono text-ink-muted hover:text-them transition-colors px-3 py-2 rounded hover:bg-ink/5">~/{{config('app.name')}}/Services</li>
                    <li wire:click="openScheduledMessages" class="cursor-pointer font-mono text-ink-muted hover:text-them transition-colors px-3 py-2 rounded hover:bg-ink/5">~/{{config('app.name')}}/Scheduled-Messages</li>
                    <li wire:click="openNotificationChannels" class="cursor-pointer font-mono text-ink-muted hover:text-them transition-colors px-3 py-2 rounded hover:bg-ink/5">~/{{config('app.name')}}/Notification-Channels</li>
                </ul>
                <livewire:component.logout />
            </div>
        </div>
    </template>
</div>
