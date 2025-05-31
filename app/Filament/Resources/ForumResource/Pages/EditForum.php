<?php

namespace App\Filament\Resources\ForumResource\Pages;

use App\Filament\Resources\ForumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditForum extends EditRecord
{
    protected static string $resource = ForumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $restrictions = [];
        $model = static::getModel()::find($data['id'])->load('restrictable');
        foreach ($model->restrictable as $restriction) {

            $restrictions[$restriction->restriction_gate_method][$restriction->restriction ] = json_decode($restriction->restriction_values);
        }
        $data['restrictions'] = $restrictions;
        return $data;
    }
}
