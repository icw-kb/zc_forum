{{-- resources/views/livewire/pages/profile/security-settings.blade.php --}}

<div class="p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-6">Security Settings</h3>
    
    <div class="space-y-6">
        {{-- Password Change Section --}}
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3.586l6.879-6.88a6 6 0 119.121-.004zM5 5l6 6"></path>
                </svg>
                Change Password
            </h4>
            <p class="text-sm text-gray-600 mb-4">Update your password to keep your account secure.</p>
            
            <form wire:submit="updatePassword" class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Current Password
                    </label>
                    <input type="password" 
                           id="current_password"
                           wire:model="current_password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror"
                           placeholder="Enter your current password">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password
                        </label>
                        <input type="password" 
                               id="password"
                               wire:model="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                               placeholder="Enter new password">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm New Password
                        </label>
                        <input type="password" 
                               id="password_confirmation"
                               wire:model="password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Confirm new password">
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                        <span wire:loading wire:target="updatePassword" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Updating...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Two-Factor Authentication Section --}}
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Two-Factor Authentication
            </h4>
            <p class="text-sm text-gray-600 mb-4">Add an extra layer of security to your account by enabling two-factor authentication.</p>
            
            @if($user->two_factor_secret)
                {{-- 2FA is enabled --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <span class="text-sm font-medium text-green-800">Two-factor authentication is enabled</span>
                                <p class="text-xs text-green-700 mt-1">Your account is protected with 2FA</p>
                            </div>
                        </div>
                        <button type="button"
                                wire:click="disable2FA"
                                wire:confirm="Are you sure you want to disable two-factor authentication? This will make your account less secure."
                                class="text-sm bg-red-100 text-red-800 px-3 py-2 rounded-md hover:bg-red-200 transition-colors">
                            Disable 2FA
                        </button>
                    </div>
                    
                    {{-- Recovery Codes --}}
                    <div class="bg-white border rounded-lg p-4">
                        <h5 class="text-sm font-medium text-gray-900 mb-2">Recovery Codes</h5>
                        <p class="text-xs text-gray-600 mb-3">Store these recovery codes in a safe place. They can be used to access your account if you lose your two-factor authentication device.</p>
                        <div class="flex space-x-2">
                            <button type="button"
                                    wire:click="showRecoveryCodes"
                                    class="text-sm bg-gray-100 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-200 transition-colors">
                                View Recovery Codes
                            </button>
                            <button type="button"
                                    wire:click="regenerateRecoveryCodes"
                                    wire:confirm="Are you sure you want to regenerate recovery codes? Your old codes will no longer work."
                                    class="text-sm bg-blue-100 text-blue-700 px-3 py-2 rounded-md hover:bg-blue-200 transition-colors">
                                Regenerate Codes
                            </button>
                        </div>
                    </div>
                </div>
            @else
                {{-- 2FA is disabled --}}
                <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <span class="text-sm font-medium text-yellow-800">Two-factor authentication is disabled</span>
                            <p class="text-xs text-yellow-700 mt-1">Protect your account with an additional verification step</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="enable2FA"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        Enable 2FA
                    </button>
                </div>
            @endif
        </div>

        {{-- Active Sessions --}}
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Browser Sessions
            </h4>
            <p class="text-sm text-gray-600 mb-4">Manage and log out your active sessions on other browsers and devices.</p>
            
            <div class="space-y-3">
                <div class="bg-white rounded-lg border p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900">{{ request()->userAgent() }}</span>
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">This device</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ request()->ip() }} • Last active: Now</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center py-2">
                    <button type="button" 
                            wire:click="logoutOtherSessions"
                            wire:confirm="Are you sure you want to log out of all other browser sessions?"
                            class="text-sm text-red-600 hover:text-red-800 transition-colors">
                        Log out other browser sessions
                    </button>
                </div>
            </div>
        </div>

        {{-- Delete Account Section --}}
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <h4 class="text-md font-medium text-red-900 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                Delete Account
            </h4>
            <p class="text-sm text-red-700 mb-4">
                Once your account is deleted, all of its resources and data will be permanently deleted. 
                Before deleting your account, please download any data or information that you wish to retain.
            </p>
            
            <button type="button"
                    wire:click="confirmAccountDeletion"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                Delete Account
            </button>
        </div>
    </div>

    {{-- Delete Account Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" wire:click="closeDeleteModal">
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg" wire:click.stop>
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900">Delete Account</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Are you sure you want to delete your account? All of your data will be permanently removed from our servers forever. This action cannot be undone.
                                        </p>
                                    </div>
                                    <div class="mt-4">
                                        <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-2">
                                            Please enter your password to confirm
                                        </label>
                                        <input type="password" 
                                               id="delete_password"
                                               wire:model="deletePassword"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('deletePassword') border-red-500 @enderror"
                                               placeholder="Enter your password">
                                        @error('deletePassword')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button"
                                    wire:click="deleteAccount"
                                    wire:loading.attr="disabled"
                                    class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto disabled:opacity-50">
                                <span wire:loading.remove wire:target="deleteAccount">Delete Account</span>
                                <span wire:loading wire:target="deleteAccount">Deleting...</span>
                            </button>
                            <button type="button"
                                    wire:click="closeDeleteModal"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 2FA Setup Modal --}}
    @if($show2FAModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="text-center">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">Enable Two-Factor Authentication</h3>
                                
                                @if($qrCode)
                                    <div class="space-y-4">
                                        <p class="text-sm text-gray-600">
                                            Scan this QR code with your authenticator app:
                                        </p>
                                        <div class="flex justify-center">
                                            {!! $qrCode !!}
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            Or enter this code manually: <span class="font-mono">{{ $manualEntryKey }}</span>
                                        </p>
                                        
                                        <div class="mt-4">
                                            <label for="two_factor_code" class="block text-sm font-medium text-gray-700 mb-2">
                                                Enter the 6-digit code from your app
                                            </label>
                                            <input type="text" 
                                                   id="two_factor_code"
                                                   wire:model="twoFactorCode"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center"
                                                   placeholder="000000"
                                                   maxlength="6">
                                            @error('twoFactorCode')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button"
                                    wire:click="confirm2FA"
                                    wire:loading.attr="disabled"
                                    class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto disabled:opacity-50">
                                <span wire:loading.remove wire:target="confirm2FA">Enable 2FA</span>
                                <span wire:loading wire:target="confirm2FA">Enabling...</span>
                            </button>
                            <button type="button"
                                    wire:click="cancel2FA"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>