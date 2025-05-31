<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Zen Cart Forum' }}</title>
    @vite(['resources/css/app.css']) {{-- Adjust if using different assets --}}
    @livewireStyles
</head>
<body class="bg-black">

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
