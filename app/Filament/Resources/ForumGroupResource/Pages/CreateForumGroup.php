<?php

namespace App\Filament\Resources\ForumGroupResource\Pages;

use App\Filament\Resources\ForumGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateForumGroup extends CreateRecord
{
    protected static string $resource = ForumGroupResource::class;

}
