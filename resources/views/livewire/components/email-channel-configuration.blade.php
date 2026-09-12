<div>
    @php
        $channel = \App\Enums\NotificationChannels::EMAIL;
    @endphp

    <x-notification-channel-card :notification-channel="$channel" :current-notification-channel="$notificationChannel"/>

    @if($showModal)
        <x-modal max-width="max-w-md" :close="'cancel'">
            <div class="flex justify-between items-center mb-6">
                <h5 class="text-them font-mono text-base">{{ $reconnect ? 'Reconnect Email' : 'Connect Email' }}</h5>
                <x-btn-primary wire:click="cancel">X</x-btn-primary>
            </div>

            <form wire:submit.prevent="submit">
                <p class="text-ink-muted text-xs font-mono mb-5">
                    @if($codeSent)
                        We sent a verification code to <span class="text-ink">{{ $email }}</span>.
                        Enter it below to confirm.
                    @elseif($reconnect)
                        Reconnect notifications for <span class="text-ink">{{ $email }}</span>.
                        Send a code to verify.
                    @else
                        Enter the email address where you want to receive notifications.
                    @endif
                </p>

                <x-form-field label="email" name="email">
                    <x-form-input type="email" wire:model="email" placeholder="you@example.com"
                                  :disabled="$reconnect || $codeSent" autocomplete="off"/>
                </x-form-field>

                @if($codeSent)
                    <x-form-field label="verification_code" name="code">
                        <x-form-input type="text" wire:model="code" placeholder="6-digit code" maxlength="6" inputmode="numeric" autocomplete="off"/>
                    </x-form-field>
                @endif

                <div class="flex justify-end items-center gap-3 mt-6">
                    <button type="button" wire:click="cancel"
                            class="px-4 py-2 rounded-md text-xs font-mono text-ink-muted hover:text-ink border border-transparent hover:border-border transition-all cursor-pointer bg-transparent">
                        Cancel
                    </button>

                    @if($codeSent)
                        <button type="button" wire:click="sendCode"
                                class="px-4 py-2 rounded-md text-xs font-mono text-ink-muted hover:text-ink border border-transparent hover:border-border transition-all cursor-pointer bg-transparent">
                            Resend code
                        </button>

                        <x-submit-button>Verify & connect</x-submit-button>
                    @else
                        <x-submit-button>Send code</x-submit-button>
                    @endif
                </div>
            </form>
        </x-modal>
    @endif
</div>
