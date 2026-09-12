<x-auth-card title="register" subtitle="Create New Account">
    <form wire:submit.prevent="save" class="space-y-10">
        <x-form-field label="name" name="name">
            <x-form-input wire:model="name" placeholder="saleh"/>
        </x-form-field>

        <x-form-field label="email" name="email">
            <x-form-input type="email" wire:model="email" placeholder="you@domain.com"/>
        </x-form-field>

        <x-form-field label="password" name="password">
            <x-form-input type="password" wire:model="password" placeholder="password"/>
        </x-form-field>

        <x-form-field label="password_confirmation" name="password_confirmation">
            <x-form-input type="password" wire:model="password_confirmation" placeholder="password_confirmation"/>
        </x-form-field>

        <x-form-field label="gender" name="gender">
            <x-gender-select/>
        </x-form-field>

        <x-submit-button class="w-full py-2.5">run ./register</x-submit-button>
    </form>

    <x-slot:footer>
        login to them account? <a href="{{ route('login') }}" wire:navigate class="text-them hover:underline hover:drop-shadow-[0_0_8px_rgba(63,185,80,0.5)] transition-all">~/{{config('app.name')}}/Login</a>
    </x-slot:footer>
</x-auth-card>
