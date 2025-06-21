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
    
    {{-- Global Search Bar - Show on plugin and forum routes --}}
    @if(request()->routeIs(['plugins.*', 'forums.*', 'search', 'home']))
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @livewire('global-search')
            </div>
        </div>
    @endif

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
