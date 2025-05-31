<?php

namespace App\Filament\Resources\PluginResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class PluginVersionRelationManager extends RelationManager
{
    protected static string $relationship = 'versions';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('version')->required(),
            Forms\Components\Textarea::make('description')->nullable(),
            Forms\Components\TextInput::make('vc_url')->nullable()->url(),
            Forms\Components\TextInput::make('count')->numeric()->default(0),
            Forms\Components\Select::make('status')
                ->options([
                    'open' => 'Open',
                    'closed' => 'Closed',
                    'locked' => 'Locked',
                    'hidden' => 'Hidden',
                ])
                ->default('open')
                ->required(),
            Forms\Components\Toggle::make('is_encapsulated'),
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name') // or 'email' if name not available
                ->default(auth()->id())
                ->required(),
            Forms\Components\Select::make('compatibleZencartVersions')
                ->label('Compatible Zencart Versions')
                ->relationship('compatibleZencartVersions', 'version') // or 'name' or a computed label
                ->multiple()
                ->preload()
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('version')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('count'),
                Tables\Columns\IconColumn::make('is_encapsulated')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
