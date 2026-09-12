<div class="grid grid-rows-[auto_1fr] lg:grid-rows-[80px_1fr] grid-cols-[minmax(0,1fr)] px-4 sm:px-10">
    <x-panel-header title="Profile">
        <x-btn-primary class="lg:hidden" wire:click="$dispatch('go-back')">←</x-btn-primary>
    </x-panel-header>
    <section class="p-3 sm:p-5 min-w-0">
        <x-command-header command="profile" flag="--edit"
                          description="Manage your account details and security settings."/>
        <div class="flex-1 min-h-0 overflow-y-auto no-scrollbar">
            <div class="max-w-3xl mx-auto pb-6">
                <div class="bg-panel border border-border rounded-xl overflow-hidden">
                    <x-form-head title="{{ auth()->user()->name }}"
                                 subtitle="Update your personal details and security credentials."
                                 status="active"/>

                    <form wire:submit.prevent="updateProfile">
                        <div class="px-4 sm:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-0">

                            {{-- Flash --}}
                            <x-flash-message class="col-span-full mb-4" :timeout="1000"/>

                            {{-- Avatar --}}
                            <x-avatar-upload class="col-span-full"
                                             target="avatar" error="avatar"
                                             :preview-url="$avatar && method_exists($avatar, 'isPreviewable') && $avatar->isPreviewable() ? $avatar->temporaryUrl() : null"
                                             :avatar-url="auth()->user()->avatar"
                                             :initials="strtoupper(substr(auth()->user()->name ?? 'U', 0, 2))"/>

                            <x-section-divider title="Account"/>

                            <x-form-field label="name" name="name">
                                <x-form-input wire:model="name" placeholder="saleh"/>
                            </x-form-field>

                            <x-form-field label="email" name="email">
                                <x-form-input type="email" wire:model="email" placeholder="example@gmail.com"/>
                            </x-form-field>

                            <x-section-divider title="Security"/>

                            <p class="col-span-full text-[11px] text-ink-muted font-mono -mt-2 mb-4 opacity-80">Only fill in these fields if you need to change your password.</p>

                            <x-form-field label="old password" name="oldPassword">
                                <x-form-input type="password" wire:model="oldPassword" placeholder="old password"/>
                            </x-form-field>

                            <x-form-field label="password" name="password">
                                <x-form-input type="password" wire:model="password" placeholder="password"/>
                            </x-form-field>

                            <x-form-field label="password_confirmation" name="password_confirmation">
                                <x-form-input type="password" wire:model="password_confirmation" placeholder="password_confirmation"/>
                            </x-form-field>

                            <x-section-divider title="Personal"/>

                            <x-form-field label="gender" name="gender" class="col-span-full">
                                <x-gender-select :value="$gender"/>
                            </x-form-field>
                        </div>

                        <x-form-footer cancel-action="closeProfile">
                            <x-submit-button>run ./update-profile</x-submit-button>
                        </x-form-footer>
                    </form>
                </div>

                <div class="bg-panel border border-border rounded-xl overflow-hidden mt-6 lg:hidden">
                    <x-form-head title="Session" subtitle="End your session and sign out of this device."
                                 status="danger" statusClass="border-danger/40 text-danger bg-danger/10"/>
                    <div class="px-4 sm:px-6 py-5">
                        <livewire:component.logout />
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
