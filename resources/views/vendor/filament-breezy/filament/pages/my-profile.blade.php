<x-filament::page>
    <div x-data="{ tab: 'account' }">
        <div class="flex border-b border-gray-200 dark:border-gray-700">
            <template x-for="(label, id) in { account: 'Account Info', password: 'Password', '2fa': 'Two Factor Auth',preferences: 'Preferences' }" :key="id">
                <button
                    type="button"
                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px"
                    :class="{
                        'border-primary-600 text-primary-600 dark:text-primary-400': tab === id,
                        'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white': tab !== id
                    }"
                    x-on:click="tab = id"
                    x-text="label"
                ></button>
            </template>
        </div>

        <div x-show="tab === 'account'" class="space-y-6 mt-6">
            @livewire('profile.personal-info')
        </div>

        <div x-show="tab === 'password'" class="space-y-6 mt-6">
            @livewire(\Jeffgreco13\FilamentBreezy\Livewire\UpdatePassword::class)
        </div>

        <div x-show="tab === '2fa'" class="space-y-6 mt-6">
            @livewire(\Jeffgreco13\FilamentBreezy\Livewire\TwoFactorAuthentication::class)
        </div>

        <div x-show="tab === 'preferences'" class="space-y-6 mt-6">
            @livewire(\App\Livewire\Profile\UserPreferencesForm::class)
        </div>
    </div>
</x-filament::page>
