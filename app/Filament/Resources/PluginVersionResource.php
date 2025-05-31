<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PluginVersionResource\Pages;
use App\Filament\Resources\PluginVersionResource\RelationManagers;
use App\Models\PluginVersion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PluginVersionResource extends Resource
{
    protected static ?string $model = PluginVersion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('version')->required()->maxLength(255),
                Forms\Components\Textarea::make('description')->nullable(),
                Forms\Components\TextInput::make('vc_url')->url()->nullable(),
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
                Forms\Components\Select::make('plugin_id')
                    ->relationship('plugin', 'name')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('compatibleZencartVersions')
                    ->label('Compatible Zencart Versions')
                    ->relationship('compatibleZencartVersions', 'version') // or 'name' or a computed label
                    ->multiple()
                    ->preload()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('version')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\IconColumn::make('is_encapsulated')->boolean(),
                Tables\Columns\TextColumn::make('count'),
                Tables\Columns\TextColumn::make('plugin.name')->label('Plugin'),
                Tables\Columns\TextColumn::make('user.name')->label('User'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPluginVersions::route('/'),
            'create' => Pages\CreatePluginVersion::route('/create'),
            'edit' => Pages\EditPluginVersion::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

}
