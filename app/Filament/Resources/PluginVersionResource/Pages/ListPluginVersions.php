<?php

namespace App\Filament\Resources\PluginVersionResource\Pages;

use App\Filament\Resources\PluginVersionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPluginVersions extends ListRecords
{
    protected static string $resource = PluginVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
