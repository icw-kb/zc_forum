<?php

namespace App\Filament\Pages;

use Jeffgreco13\FilamentBreezy\Pages\MyProfilePage;


class AccountSettingsPage extends MyProfilePage
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'vendor.filament-breezy.filament.pages.my-profile';

    protected function getFormSchema(): array
    {
        return parent::getFormSchema(); // No changes, should show original form
    }
}
