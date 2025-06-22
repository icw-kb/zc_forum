<div x-data="{ show: $wire.entangle('open') }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 transition-opacity">
    <div @click.away="show = false" class="bg-white dark:bg-gray-800 w-full max-w-md p-6 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Reset Password</h2>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Enter your email address and we'll send you a link to reset your password.
            </p>
        </div>

        <form wire:submit.prevent="sendResetLink" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                <input wire:model.defer="email" 
                       type="email" 
                       id="email" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 dark:border-red-500 @enderror"
                       placeholder="Enter your email address" />
                @error('email') 
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-6 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm">
                    <span wire:loading.remove>Send Reset Link</span>
                    <span wire:loading class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    </span>
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Remember your password? 
                    <button type="button" 
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors font-medium" 
                            wire:click.prevent="$dispatch('open-login')">
                        Sign in
                    </button>
                </p>
            </div>
        </form>
    </div>
</div>