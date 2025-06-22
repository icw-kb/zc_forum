{{-- resources/views/livewire/pages/profile/personal-information.blade.php --}}

<div class="p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Personal Information</h3>
    
    {{-- Email Verification Notice --}}
    @if(!$user->hasVerifiedEmail())
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-900 dark:text-yellow-100">Email Verification Required</h3>
                    <div class="mt-2 text-sm text-yellow-800 dark:text-yellow-100">
                        <p>Please verify your email address: <strong>{{ $user->email }}</strong></p>
                    </div>
                    <div class="mt-3">
                        <button type="button" 
                                wire:click="resendEmailVerification"
                                class="text-sm bg-yellow-700 dark:bg-yellow-600 text-white px-3 py-2 rounded-md hover:bg-yellow-800 dark:hover:bg-yellow-700 transition-colors">
                            Resend Verification Email
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Email Change Warning --}}
    @if($emailChanged)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100">Email Change Detected</h3>
                    <div class="mt-2 text-sm text-blue-800 dark:text-blue-100">
                        <p>You've changed your email from <strong>{{ $originalEmail }}</strong> to <strong>{{ $email }}</strong>.</p>
                        <p class="mt-1">After saving, you'll need to verify your new email address.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <form wire:submit="savePersonalInfo" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Name Field --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Full Name
                </label>
                <input type="text" 
                       id="name"
                       wire:model="name"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 dark:border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email Field --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Email Address
                    @if(!$user->hasVerifiedEmail())
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-600 text-white ml-2">
                            Unverified
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white ml-2">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Verified
                        </span>
                    @endif
                </label>
                <input type="email" 
                       id="email"
                       wire:model="email"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 dark:border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @if($emailChanged)
                    <p class="mt-1 text-sm text-blue-600 dark:text-blue-400">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        This email will need verification after saving
                    </p>
                @endif
            </div>
        </div>

        {{-- Bio Field --}}
        <div>
            <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Bio
            </label>
            <textarea id="bio"
                      wire:model="bio"
                      rows="4"
                      placeholder="Tell us a little about yourself..."
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none @error('bio') border-red-500 dark:border-red-500 @enderror"></textarea>
            @error('bio')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Location Field --}}
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Location
                </label>
                <input type="text" 
                       id="location"
                       wire:model="location"
                       placeholder="City, Country"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('location') border-red-500 dark:border-red-500 @enderror">
                @error('location')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Website Field --}}
            <div>
                <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Website
                </label>
                <input type="url" 
                       id="website"
                       wire:model="website"
                       placeholder="https://your-website.com"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('website') border-red-500 dark:border-red-500 @enderror">
                @error('website')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex justify-end">
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-6 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm"
                <span wire:loading.remove>
                    @if($emailChanged)
                        Save Changes & Send Verification
                    @else
                        Save Changes
                    @endif
                </span>
                <span wire:loading class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                </span>
            </button>
        </div>
    </form>
</div>