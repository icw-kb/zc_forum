<?php

namespace App\Filament\Resources\ForumGroupResource\Pages;

use App\Filament\Resources\ForumGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditForumGroup extends EditRecord
{
    protected static string $resource = ForumGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
