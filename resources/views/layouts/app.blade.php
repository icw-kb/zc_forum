<!DOCTYPE html>
<html lang="en" class="{{ auth()->check() ? (auth()->user()->getPreference('ui.dark_mode') ? 'dark' : '') : 'dark' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Zen Cart Forum' }}</title>
    @vite(['resources/css/app.css']) {{-- Adjust if using different assets --}}
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900">

<div>
    <x-header />

<main class="p-4">
    {{ $slot }}
</main>


 <x-footer-mega-menu />
</div>
    <livewire:components.toast-notifications />
    @livewire('auth.login-modal') 
    @livewire('auth.forgot-password-modal')
    @livewire('auth.register-modal')
    @livewireScripts
</body>
</html>
