@props(['value' => null])

<div class="flex items-center gap-6 bg-[var(--color-panel)] border border-border rounded-md px-4 py-3">
    <label class="flex items-center gap-2 text-xs text-ink-muted font-mono cursor-pointer">
        <input type="radio" name="gender" wire:click="setGender('m')" class="accent-them" @checked($value === 'm')>
        male
    </label>
    <label class="flex items-center gap-2 text-xs text-ink-muted font-mono cursor-pointer">
        <input type="radio" name="gender" wire:click="setGender('f')" class="accent-them" @checked($value === 'f')>
        female
    </label>
</div>
