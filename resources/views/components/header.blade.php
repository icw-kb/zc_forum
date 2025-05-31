<header class="bg-black text-white w-full">
    <div class="max-w-screen-xl mx-auto flex justify-between items-center py-4 px-6">
        <div class="flex-shrink-0">
            <a href="/">
                <img src="/images/logos/zen-cart-hr-transparent-dark.png" alt="Zen Cart Logo" class="h-16 w-auto">
            </a>
        </div>

        {{-- Left-side nav --}}
        <nav class="flex items-center space-x-6">
            <a href="/forums" class="flex flex-col items-center group transition-all duration-300 ease-in-out">
                <svg class="w-8 h-8 text-white group-hover:text-blue-400 transition-colors duration-300"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                        d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="mt-1 text-sm group-hover:text-blue-400 transition-colors duration-300">Community</span>
            </a>

            <a href="/plugins" class="flex flex-col items-center group transition-all duration-300 ease-in-out">
                <svg class="w-8 h-8 text-white group-hover:text-green-400 transition-colors duration-300"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4" />
                </svg>
                <span class="mt-1 text-sm group-hover:text-green-400 transition-colors duration-300">Plugins</span>
            </a>

            <a href="https://github.com/zencart/zencart"
                class="flex flex-col items-center group transition-all duration-300 ease-in-out" target="_blank">
                <svg class="w-8 h-8 text-white group-hover:text-purple-400 transition-colors duration-300"
                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd"
                        d="M12.006 2a9.847 9.847 0 0 0-6.484 2.44 10.32 10.32 0 0 0-3.393 6.17 10.48 10.48 0 0 0 1.317 6.955 10.045 10.045 0 0 0 5.4 4.418c.504.095.683-.223.683-.494 0-.245-.01-1.052-.014-1.908-2.78.62-3.366-1.21-3.366-1.21a2.711 2.711 0 0 0-1.11-1.5c-.907-.637.07-.621.07-.621.317.044.62.163.885.346.266.183.487.426.647.71.135.253.318.476.538.655a2.079 2.079 0 0 0 2.37.196c.045-.52.27-1.006.635-1.37-2.219-.259-4.554-1.138-4.554-5.07a4.022 4.022 0 0 1 1.031-2.75 3.77 3.77 0 0 1 .096-2.713s.839-.275 2.749 1.05a9.26 9.26 0 0 1 5.004 0c1.906-1.325 2.74-1.05 2.74-1.05.37.858.406 1.828.101 2.713a4.017 4.017 0 0 1 1.029 2.75c0 3.939-2.339 4.805-4.564 5.058a2.471 2.471 0 0 1 .679 1.897c0 1.372-.012 2.477-.012 2.814 0 .272.18.592.687.492a10.05 10.05 0 0 0 5.388-4.421 10.473 10.473 0 0 0 1.313-6.948 10.32 10.32 0 0 0-3.39-6.165A9.847 9.847 0 0 0 12.007 2Z"
                        clip-rule="evenodd" />
                </svg>
                <span class="mt-1 text-sm group-hover:text-purple-400 transition-colors duration-300">Collaborate</span>
            </a>

            <a href="https://docs.zen-cart.com"
                class="flex flex-col items-center group transition-all duration-300 ease-in-out" target="_blank">
                <svg class="w-8 h-8 text-white group-hover:text-orange-400 transition-colors duration-300"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.03v13m0-13c-2.819-.831-4.715-1.076-8.029-1.023A.99.99 0 0 0 3 6v11c0 .563.466 1.014 1.03 1.007 3.122-.043 5.018.212 7.97 1.023m0-13c2.819-.831 4.715-1.076 8.029-1.023A.99.99 0 0 1 21 6v11c0 .563-.466 1.014-1.03 1.007-3.122-.043-5.018.212-7.97 1.023" />
                </svg>
                <span class="mt-1 text-sm group-hover:text-orange-400 transition-colors duration-300">Learn</span>
            </a>
        </nav>
        {{-- Right-side auth buttons --}}
        <div class="flex items-center space-x-4">
            @auth
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                        <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                            alt="Avatar" class="w-8 h-8 rounded-full">
                        <span class="text-sm hidden md:inline">{{ Auth::user()->name }}</span>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white rounded shadow-lg z-50 text-sm text-gray-800">
                        <a href="/profile" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 hover:bg-gray-100">Log out</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="flex items-center space-x-3 ml-6">
                    <a href="#" onclick="window.dispatchEvent(new CustomEvent('open-login-modal'))"
                        class="px-4 py-1.5 bg-white text-black text-sm font-semibold rounded-md shadow hover:bg-gray-100 transition">
                        Login
                    </a>
                    <a href="#" onclick="window.dispatchEvent(new CustomEvent('open-register-modal'))"
                        class="px-4 py-1.5 bg-blue-600 text-white text-sm font-semibold rounded-md shadow hover:bg-blue-700 transition">
                        Register
                    </a>
                </div>
            @endauth
        </div>
    </div>
</header>