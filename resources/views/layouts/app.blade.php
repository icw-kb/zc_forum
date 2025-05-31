<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Zen Cart Forum' }}</title>
    @vite(['resources/css/app.css']) {{-- Adjust if using different assets --}}
    @livewireStyles
</head>
<body class="bg-white text-gray-900 antialiased">

<div>
    <x-header />

<main class="border p-4">
    {{ $slot }}
</main>



</div>
    @livewire('auth.login-modal') 
    @livewire('auth.forgot-password-modal')
    @livewire('auth.register-modal')
    @livewireScripts
</body>
</html>
