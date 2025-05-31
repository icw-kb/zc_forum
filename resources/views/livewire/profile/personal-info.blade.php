<div class="space-y-4">
    <div>
        <h2 class="text-lg font-bold tracking-tight">
            {{ __('filament-breezy::default.profile.personal_info.heading') }}
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('filament-breezy::default.profile.personal_info.subheading') }}
        </p>
    </div>

    <x-filament::card>
        <form wire:submit.prevent="updateProfile" class="space-y-6">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-filament::button type="submit">
                    {{ __('filament-breezy::default.profile.personal_info.submit.label') }}
                </x-filament::button>
            </div>
        </form>
    </x-filament::card>
</div>
