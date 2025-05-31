<?php

namespace App\Filament\Resources\PluginVersionResource\Pages;

use App\Filament\Resources\PluginVersionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPluginVersion extends EditRecord
{
    protected static string $resource = PluginVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
