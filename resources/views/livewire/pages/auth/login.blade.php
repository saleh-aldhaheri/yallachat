<x-auth-card title="login" subtitle="Authenticate to continue.">
    <x-flash-message class="mb-6" :timeout="null"/>

    <form wire:submit.prevent="save" class="space-y-10">
        <x-form-field label="email" name="email">
            <x-form-input type="email" wire:model="email" placeholder="you@domain.com"/>
        </x-form-field>

        <x-form-field label="password" name="password">
            <x-form-input type="password" wire:model="password" placeholder="&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;&#x2022;"/>
        </x-form-field>

        <x-submit-button class="w-full py-2.5">run ./login</x-submit-button>
    </form>

    <x-slot:footer>
        no account yet? <a href="{{ route('register') }}" wire:navigate class="text-them hover:underline hover:drop-shadow-[0_0_8px_rgba(63,185,80,0.5)] transition-all">~/{{config('app.name')}}t/Register</a>
    </x-slot:footer>
</x-auth-card>
