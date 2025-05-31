{{-- resources/views/livewire/pages/profile/security-settings.blade.php --}}

<div class="p-6">
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Security Settings</h2>
        <p class="text-gray-600 text-sm">Manage your password and account security.</p>
    </div>

    <div class="space-y-8">
        {{-- Change Password Section --}}
        <div class="border-b border-gray-200 pb-8">
            <h3 class="text-md font-medium text-gray-900 mb-4">Change Password</h3>
            
            <form wire:submit="updatePassword" class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" 
                           id="current_password"
                           wire:model="current_password"
                           class="w-full max-w-md px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    @error('current_password') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" 
                               id="new_password"
                               wire:model="new_password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('new_password') 
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" 
                               id="new_password_confirmation"
                               wire:model="new_password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                </div>

                <div class="flex items-center">
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            wire:target="updatePassword"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                        <span wire:loading wire:target="updatePassword">Updating...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Two-Factor Authentication Section --}}
        <div class="border-b border-gray-200 pb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-md font-medium text-gray-900">Two-Factor Authentication</h3>
                    <p class="text-sm text-gray-600">Add an extra layer of security to your account.</p>
                </div>
                <div class="flex items-center">
                    @if($user->two_factor_secret)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Enabled
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Disabled
                        </span>
                    @endif
                </div>
            </div>

            @if($user->two_factor_secret)
                <div class="space-y-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-green-800">
                                    Two-factor authentication is enabled. Your account is protected.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button wire:click="showRecoveryCodes" 
                                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                            Show Recovery Codes
                        </button>
                        <button wire:click="regenerateRecoveryCodes" 
                                class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
                            Regenerate Codes
                        </button>
                        <button wire:click="disableTwoFactor" 
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                            Disable 2FA
                        </button>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-800">
                                    Two-factor authentication is not enabled. Consider enabling it for better security.
                                </p>
                            </div>
                        </div>
                    </div>

                    <button wire:click="enableTwoFactor" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Enable Two-Factor Authentication
                    </button>
                </div>
            @endif
        </div>

        {{-- Active Sessions Section --}}
        <div class="border-b border-gray-200 pb-8">
            <h3 class="text-md font-medium text-gray-900 mb-4">Active Sessions</h3>
            <p class="text-sm text-gray-600 mb-4">Manage and log out your active sessions on other browsers and devices.</p>

            <div class="space-y-3">
                @forelse($activeSessions ?? [] as $session)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                @if(str_contains(strtolower($session['device']), 'mobile'))
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $session['device'] }}</p>
                                <p class="text-xs text-gray-500">{{ $session['ip_address'] }} • {{ $session['location'] }}</p>
                                <p class="text-xs text-gray-500">Last active {{ $session['last_active'] }}</p>
                            </div>
                        </div>
                        @if($session['is_current'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Current Session
                            </span>
                        @else
                            <button wire:click="logoutSession('{{ $session['id'] }}')" 
                                    class="text-red-600 hover:text-red-900 text-sm">
                                Revoke
                            </button>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No other active sessions found.</p>
                @endforelse
            </div>

            @if(count($activeSessions ?? []) > 1)
                <div class="mt-4">
                    <button wire:click="logoutAllSessions" 
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                        Log Out All Other Sessions
                    </button>
                </div>
            @endif
        </div>

        {{-- Delete Account Section --}}
        <div>
            <h3 class="text-md font-medium text-red-900 mb-4">Delete Account</h3>
            <p class="text-sm text-gray-600 mb-4">
                Once your account is deleted, all of its resources and data will be permanently deleted. 
                Before deleting your account, please download any data or information that you wish to retain.
            </p>

            <button wire:click="confirmAccountDeletion" 
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                Delete Account
            </button>
        </div>
    </div>
</div>