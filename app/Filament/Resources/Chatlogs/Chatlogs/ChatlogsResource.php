<?php

namespace App\Filament\Resources\Chatlogs\Chatlogs;

use App\Filament\Resources\Chatlogs\Pages;
use App\Models\ChatMessage;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use App\Models\Player;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class ChatlogsResource extends Resource
{
    protected static ?string $model = ChatMessage::class;

    protected static string|null|\backedenum $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sender')
                    ->schema([
                        FileUpload::make('sender')
                            ->image()
                            ->hiddenLabel(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Sender')
                    ->getStateUsing(function ($record) {
                        $player = Player::where('uuid', $record->sender)->first();
                        return $player
                            ? "https://nmsr.nickac.dev/bust/" . ($player->uuid ?? "00000000-0000-0000-0000-000000000000")
                            : 'https://example.com/default-avatar.png';
                    })
                    ->url(fn ($record) => route('filament.admin.resources.players.view', $record->sender))
                    ->size(24),

                TextColumn::make('username')
                    ->label('Username')
                    ->getStateUsing(fn ($record) =>
                    $record->sender
                        ? Player::getNameFromUuid($record->sender)
                        : 'Unknown'
                    )
                    ->url(fn ($record) => route('filament.admin.resources.players.view', $record->sender))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('message')
                    ->label('Message')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('server')
                    ->label('Server')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('short_time')
                    ->label('Short_Time')
                    ->alignRight()
                    ->sortable(),
            ])
            ->defaultSort('sent_at', 'desc')
            ->paginationPageOptions([10, 25, 50])
            ->poll('2s')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatlogs::route('/'),
            'view' => Pages\ViewChatlogs::route('/{record}'),
        ];
    }
}
