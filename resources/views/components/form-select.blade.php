<select {{ $attributes->merge(['class' => 'w-full bg-[var(--color-panel)] border border-border rounded-md px-3 py-2.5 text-ink font-mono text-[13px] outline-none focus:border-them/60 transition-colors appearance-none cursor-pointer']) }}
        style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2710%27 height=%276%27%3E%3Cpath d=%27M0 0l5 6 5-6%27 fill=%27%236e7681%27/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right 12px center;">
    {{ $slot }}
</select>
