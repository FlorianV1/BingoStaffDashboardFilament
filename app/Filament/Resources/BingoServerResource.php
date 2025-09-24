<?php

namespace App\Filament\Resources;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Actions;
use Filament\Tables\Columns\Layout\Split;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use App\Filament\Resources\BingoServerResource\Pages;
use App\Models\BingoServer;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;

class BingoServerResource extends Resource
{
    protected static ?string $model = BingoServer::class;

    // Fixed: Simplified navigation icon type for v4 compatibility
    protected static string|null|\backedenum $navigationIcon = 'heroicon-o-rectangle-stack';

    // Fixed: Updated method signature and corrected parameter usage
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')->disabled(),
                Forms\Components\TextInput::make('ip_address')->nullable(),
                Forms\Components\TextInput::make('port')->numeric()->default(8080),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Copied ID')
                    ->copyMessageDuration(1500),
                TextColumn::make('ip')
                    ->sortable()
                    ->label('IP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('copied IP')
                    ->copyMessageDuration(1500),
                TextColumn::make('port')
                    ->label('Port')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Port copied')
                    ->copyMessageDuration(1500),
                TextColumn::make('variant')
                    ->label('Variant')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Variant copied')
                    ->copyMessageDuration(1500),
                TextColumn::make('state')
                    ->label('State')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('State copied')
                    ->copyMessageDuration(1500),
                TextColumn::make('players')
                    ->label('Players')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Players copied')
                    ->copyMessageDuration(1500),
                IconColumn::make('restricted')
                    ->sortable()
                    ->label('Restricted')
                    ->boolean(),
                ImageColumn::make('bound_to')
                    ->label('Player')
                    ->getStateUsing(fn ($record) =>
                    $record->bound_to ? "https://nmsr.nickac.dev/bust/{$record->bound_to}" : null
                    )
                    ->size(25)
                    ->toggleable(),
                TextColumn::make('join_code')
                    ->label('Join Code')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('copied Join Code')
                    ->copyMessageDuration(1500),
            ])
            ->defaultPaginationPageOption(25)
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListBingoServers::route('/'),
            'view' => Pages\ViewBingoServer::route('/{record}'),
        ];
    }
}
