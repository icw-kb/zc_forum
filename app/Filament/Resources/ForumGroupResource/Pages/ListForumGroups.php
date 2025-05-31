<?php

namespace App\Filament\Resources\ForumGroupResource\Pages;

use App\Filament\Resources\ForumGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListForumGroups extends ListRecords
{
    protected static string $resource = ForumGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
