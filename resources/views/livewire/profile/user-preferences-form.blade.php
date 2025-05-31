<div class="space-y-4">
    <div>
        <h2 class="text-lg font-bold tracking-tight">
            {{ __('User Preferences') }}
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Manage how the application behaves for your account.') }}
        </p>
    </div>

    <x-filament::card>
        <form wire:submit.prevent="submit" class="space-y-6">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-filament::button type="submit">
                    {{ __('Save Preferences') }}
                </x-filament::button>
            </div>
        </form>
    </x-filament::card>
</div>
