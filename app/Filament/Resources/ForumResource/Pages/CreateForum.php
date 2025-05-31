<?php

namespace App\Filament\Resources\ForumResource\Pages;

use App\Filament\Resources\ForumResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateForum extends CreateRecord
{
    protected static string $resource = ForumResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }

}
