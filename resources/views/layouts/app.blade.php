<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="icon" type="image/svg+xml" href="/images/yalla-chat-icon-dark.svg">
        <meta name="theme-color" content="#0d1117">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-page text-ink font-mono antialiased">
        {{ $slot }}
        @livewireScripts
        <x-toaster-hub />
    </body>
</html>
