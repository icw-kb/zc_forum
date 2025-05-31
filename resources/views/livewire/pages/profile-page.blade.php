{{-- resources/views/livewire/pages/profile-page.blade.php --}}

<div class="min-h-screen bg-gray-500" x-data="{ activeTab: @entangle('activeTab') }">
    <div class="max-w-4xl mx-auto py-8 px-4">
        {{-- Profile Header - Use solid colors --}}
        <div class="bg-gray-100 rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start space-x-6">
                {{-- Avatar Section --}}
                <div class="relative" x-data="{ 
                    showAvatarMenu: false,
                    avatarPreview: @entangle('avatarPreview')
                }">
                    <div class="w-24 h-24 rounded-xl bg-gray-200 overflow-hidden border-2 border-gray-300 shadow-lg">
                        @if($avatarPreview)
                            <img src="{{ $avatarPreview }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @elseif($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white text-2xl font-semibold bg-gradient-to-br from-blue-500 to-purple-600">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    {{-- Avatar Actions --}}
                    <div class="absolute -bottom-1 -right-1">
                        <button @click="showAvatarMenu = !showAvatarMenu" 
                                class="bg-blue-600 text-white p-2 rounded-lg cursor-pointer hover:bg-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                        
                        {{-- Avatar Menu --}}
                        <div x-show="showAvatarMenu" 
                             x-transition
                             @click.away="showAvatarMenu = false"
                             class="absolute bottom-14 right-0 bg-gray-100 rounded-xl shadow-lg border border-gray-200 py-2 w-36 z-10">
                            <label class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 cursor-pointer rounded-lg mx-1">
                                Upload Photo
                                <input type="file" wire:model="avatar" accept="image/*" class="hidden">
                            </label>
                            @if($user->avatar)
                                <button wire:click="removeAvatar" 
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg mx-1">
                                    Remove Photo
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Upload Progress --}}
                    <div wire:loading wire:target="avatar" class="absolute inset-0 bg-gray-900 bg-opacity-50 rounded-xl flex items-center justify-center">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-white"></div>
                    </div>
                </div>
                
                {{-- User Info --}}
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h1>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    @if($user->bio)
                        <p class="text-gray-700 mt-2 leading-relaxed">{{ $user->bio }}</p>
                    @endif
                    <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500">
                        @if($user->location)
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $user->location }}
                            </span>
                        @endif
                        <span>Member since {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="bg-gray-100 rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="border-b border-gray-300">
                <nav class="flex space-x-8 px-6">
                    <button @click="activeTab = 'personal'"
                            :class="{ 'border-blue-500 text-blue-600 bg-blue-50': activeTab === 'personal', 'border-transparent text-gray-600 hover:text-gray-800': activeTab !== 'personal' }"
                            class="py-4 px-3 border-b-2 font-medium text-sm transition-all duration-200 rounded-t-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Personal Information</span>
                        </div>
                    </button>
                    
                    <button @click="activeTab = 'security'"
                            :class="{ 'border-blue-500 text-blue-600 bg-blue-50': activeTab === 'security', 'border-transparent text-gray-600 hover:text-gray-800': activeTab !== 'security' }"
                            class="py-4 px-3 border-b-2 font-medium text-sm transition-all duration-200 rounded-t-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span>Security</span>
                        </div>
                    </button>
                    
                    <button @click="activeTab = 'preferences'"
                            :class="{ 'border-blue-500 text-blue-600 bg-blue-50': activeTab === 'preferences', 'border-transparent text-gray-600 hover:text-gray-800': activeTab !== 'preferences' }"
                            class="py-4 px-3 border-b-2 font-medium text-sm transition-all duration-200 rounded-t-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Preferences</span>
                        </div>
                    </button>
                </nav>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="bg-gray-100 rounded-xl shadow-sm border border-gray-200">
            {{-- Personal Information Tab --}}
            <div x-show="activeTab === 'personal'" x-transition>
                @include('livewire.pages.profile.personal-information')
            </div>

            {{-- Security Tab --}}
            <div x-show="activeTab === 'security'" x-transition>
                @include('livewire.pages.profile.security-settings')
            </div>

            {{-- Preferences Tab --}}
            <div x-show="activeTab === 'preferences'" x-transition>
                @include('livewire.pages.profile.preferences')
            </div>
        </div>

        {{-- Success Message --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition
                 x-init="setTimeout(() => show = false, 3000)"
                 class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50">
                {{ session('success') }}
            </div>
        @endif
    </div>
</div>