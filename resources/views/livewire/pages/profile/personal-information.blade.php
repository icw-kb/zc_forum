{{-- resources/views/livewire/pages/profile/personal-information.blade.php --}}

<div class="p-6">
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">Personal Information</h2>
        <p class="text-gray-600 text-sm">Update your account's profile information and email address.</p>
    </div>

    <form wire:submit="savePersonalInfo" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" 
                       id="name"
                       wire:model="name"
                       class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 backdrop-blur-sm"
                       placeholder="Your full name">
                @error('name') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" 
                       id="email"
                       wire:model="email"
                       class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 backdrop-blur-sm"
                       placeholder="your.email@example.com">
                @error('email') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                @enderror
            </div>
        </div>

        {{-- Bio --}}
        <div>
            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
            <textarea id="bio"
                      wire:model="bio"
                      rows="4"
                      class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 resize-none backdrop-blur-sm"
                      placeholder="Tell us a little about yourself..."></textarea>
            <p class="text-xs text-gray-500 mt-1">{{ strlen($bio ?? '') }}/500 characters</p>
            @error('bio') 
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Location --}}
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input type="text" 
                       id="location"
                       wire:model="location"
                       class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 backdrop-blur-sm"
                       placeholder="City, Country">
                @error('location') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                @enderror
            </div>

            {{-- Website --}}
            <div>
                <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                <input type="url" 
                       id="website"
                       wire:model="website"
                       class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 backdrop-blur-sm"
                       placeholder="https://example.com">
                @error('website') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                @enderror
            </div>
        </div>

        {{-- Email Verification Notice --}}
        @if(is_null($user->email_verified_at))
            <div class="bg-yellow-50/80 backdrop-blur-sm border border-yellow-200/50 rounded-xl p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-800">
                            Your email address is unverified.
                            <a href="{{ route('verification.send') }}" class="underline text-yellow-800 hover:text-yellow-900 ml-1">
                                Click here to re-send the verification email.
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between pt-6 border-t border-gray-200/30">
            <div class="flex items-center">
                <div wire:loading wire:target="savePersonalInfo" class="flex items-center text-blue-600">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                </div>
            </div>
            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500/50 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 shadow-lg">
                Save Changes
            </button>
        </div>
    </form>
</div>