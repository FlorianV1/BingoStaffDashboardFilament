<?php

namespace App\Filament\Resources\Players\Players;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Actions;
use Filament\Tables\Columns\Layout\Split;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use App\Filament\Resources\Players\Pages;
use App\Models\Player;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Tables\Columns\Column;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\RepeatableEntry;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-user-group';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Player Overview')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('avatar')
                                    ->label('')
                                    ->getStateUsing(fn ($record) =>
                                        "https://nmsr.nickac.dev/bust/" . ($record->uuid ?? "00000000-0000-0000-0000-000000000000")
                                    )
                                    ->hiddenLabel()
                                    ->size('20rem'),

                                Group::make([
                                    TextEntry::make('name')
                                        ->label('Username')
                                        ->weight(FontWeight::Bold)
                                        ->copyable(),

                                    TextEntry::make('uuid')
                                        ->label('UUID')
                                        ->copyable(),

                                    TextEntry::make('discordConnection.discord_id')
                                        ->label('Discord ID')
                                        ->copyable()
                                        ->getStateUsing(fn ($record) => $record->discordConnection?->discord_id ?? 'Not connected')
                                        ->prefixAction(
                                            fn ($record) => $record->discordConnection?->discord_id
                                                ? Action::make('view')
                                                    ->url('https://discord.com/users/' . $record->discordConnection->discord_id, true)
                                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                                : null
                                        ),

                                    TextEntry::make('groups')
                                        ->label('Groups')
                                        ->getStateUsing(function ($record) {
                                            $roles = $record->permissions
                                                ->pluck('permission')
                                                ->filter(fn ($perm) => str_starts_with($perm, 'group.'))
                                                ->unique()
                                                ->values();

                                            $map = \App\Models\LuckpermsUserPermission::renamePermissions();

                                            return $roles->map(function ($role) use ($map) {
                                                if (!isset($map[$role])) {
                                                    return null;
                                                }

                                                $info = $map[$role];
                                                $class = match($info['color']) {
                                                    'red' => 'bg-red-500 text-white',
                                                    'black' => 'bg-black text-white',
                                                    'purple' => 'bg-purple-600 text-white',
                                                    'orange' => 'bg-orange-500 text-white',
                                                    'teal' => 'bg-teal-500 text-white',
                                                    'indigo' => 'bg-indigo-500 text-white',
                                                    'yellow' => 'bg-yellow-400 text-black',
                                                    'green' => 'bg-green-500 text-white',
                                                    'pink' => 'bg-pink-400 text-black',
                                                    'blue' => 'bg-blue-500 text-white',
                                                    'cyan' => 'bg-cyan-400 text-black',
                                                    default => 'bg-slate-500 text-white',
                                                };

                                                return "<span class='inline-block text-sm font-semibold rounded px-2 py-1 {$class}'>{$info['name']}</span>";
                                            })->filter()->implode(' ');
                                        })
                                        ->html(),

                                ])->columns(1),

                                Group::make([
                                    TextEntry::make('created_at')
                                        ->label('First Joined')
                                        ->dateTime(),

                                    TextEntry::make('last_joined')
                                        ->label('Last Joined')
                                        ->getStateUsing(fn ($record) =>
                                        optional($record->sessions->sortByDesc('session_start')->first())->session_start
                                        )
                                        ->dateTime(),

                                    TextEntry::make('server')
                                        ->label('Server')
                                        ->getStateUsing(fn ($record) =>
                                            optional($record->sessions->sortByDesc('session_start')->first())->getAttribute('server') ?? 'N/A'
                                        )
                                        ->url(fn ($record) =>
                                        optional($record->sessions->sortByDesc('session_start')->first())->session_end === null
                                            ? '/server/' . (optional($record->sessions->sortByDesc('session_start')->first())->getAttribute('server') ?? '')
                                            : null
                                        )
                                        ->openUrlInNewTab(),

                                    TextEntry::make('country_code_raw')
                                        ->label('Country Code')
                                        ->getStateUsing(fn ($record) => strtoupper($record->latestSession?->country_code ?? 'N/A')),
                                ])->columns(1),
                            ]),
                    ])
                    ->icon('heroicon-o-user')
                    ->headerActions([
                        Action::make('status')
                            ->label(function ($record) {
                                $online = $record->sessions->sortByDesc('session_start')->first()?->session_end === null;
                                return $online ? 'Online' : 'Offline';
                            })
                            ->badge()
                            ->icon(function ($record) {
                                $online = $record->sessions->sortByDesc('session_start')->first()?->session_end === null;
                                return $online ? 'heroicon-s-signal' : 'heroicon-s-signal-slash';
                            })
                            ->color(function ($record) {
                                $online = $record->sessions->sortByDesc('session_start')->first()?->session_end === null;
                                return $online ? 'success' : 'gray';
                            }),
                    ]),

                Section::make('Statistics')
                    ->schema([
                        Grid::make(2)->schema([
                            KeyValueEntry::make('bingo_stats_multiplayer')
                                ->label('Multiplayer Stats')
                                ->getStateUsing(fn ($record) => $record->getBingoStatsSummary()['multiplayer'] ?? []),

                            KeyValueEntry::make('bingo_stats_singleplayer')
                                ->label('Singleplayer Stats')
                                ->getStateUsing(fn ($record) => $record->getBingoStatsSummary()['singleplayer'] ?? []),
                        ]),

                        KeyValueEntry::make('bingo_stats_extra')
                            ->label('Extra Stats')
                            ->getStateUsing(fn ($record) => $record->getBingoStatsSummary()['extra'] ?? []),
                    ])
                    ->icon('heroicon-o-chart-bar'),

                Section::make('Commands')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        Actions::make([
                            Action::make('previousPage')
                                ->label('<')
                                ->disabled(fn ($livewire) => $livewire->chatPage <= 1)
                                ->action(fn ($livewire) => $livewire->chatPage--),

                            Action::make('nextPage')
                                ->label('>')
                                ->disabled(function ($record, $livewire) {
                                    $totalMessages = $record->chatMessages()->count();
                                    $totalPages = (int) ceil($totalMessages / 10);
                                    return $livewire->chatPage >= $totalPages;
                                })
                                ->action(fn ($livewire) => $livewire->chatPage++),
                        ]),

                        Split::make([
                            RepeatableEntry::make('chatMessages')
                                ->label('Latest Chat Messages')
                                ->hiddenLabel()
                                ->getStateUsing(function ($record, $livewire) {
                                    $page = $livewire->chatPage ?? 1;

                                    return $record->chatMessages()
                                        ->skip(($page - 1) * 10)
                                        ->take(10)
                                        ->get()
                                        ->values();
                                })
                                ->schema([
                                    TextEntry::make('sent_at')
                                        ->label('Time / Server ID')
                                        ->formatStateUsing(function ($state, $record) {
                                            $timestamp = optional($record->sent_at)?->format('Y-m-d H:i') ?? 'Unknown Time';
                                            $serverId = $record->getAttribute('server') ?? 'Unknown';
                                            return "$timestamp | $serverId";
                                        })
                                        ->html(),

                                    Grid::make()
                                        ->columns(12)
                                        ->schema([
                                            TextEntry::make('message')
                                                ->label('Message')
                                                ->columnSpan(8)
                                                ->grow(false)
                                                ->formatStateUsing(fn ($state) => nl2br(e($state)))
                                                ->html(),

                                            Actions::make([
                                                Action::make('viewServer')
                                                    ->label(fn ($record) => $record->getAttribute('server') ?? 'Unknown Server')
                                                    ->icon('heroicon-o-server-stack')
                                                    ->modalHeading('Server Information')
                                                    ->modalSubmitAction(false)
                                                    ->action(fn () => null)
                                                    ->modalContent(function ($record) {
                                                        $serverId = $record->server;
                                                        $server = \App\Models\BingoServer::find($serverId);
                                                        return view('filament.server-id-modal', [
                                                            'server' => $server,
                                                        ]);
                                                    }),
                                            ])
                                                ->columnSpan(2),
                                        ]),
                                ]),
                        ]),
                    ]),

                Section::make('Punishments')
                    ->schema([

                    ])
                    ->icon('heroicon-o-shield-exclamation'),
            ]);
    }

    // Fixed: Updated method signature for v4 - now uses Schema instead of Form
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->disabled()
                            ->default(fn($player) => $player->name),

                        TextInput::make('uuid')
                            ->label('Minecraft UUID')
                            ->disabled()
                            ->default(fn($player) => $player->uuid),

                        TextInput::make('country_code')
                            ->label('Country')
                            ->disabled()
                            ->default(fn($record) => $record->latestSession?->country_code ?? 'Unknown')
                            ->formatStateUsing(function ($state) {
                                return Cache::remember("country_flag_{$state}", now()->addMinutes(10), function () use ($state) {
                                    return ($state !== 'Unknown' && $state !== null
                                        ? self::countryFlagEmoji($state) . ' ' . strtoupper($state)
                                        : 'Unknown');
                                });
                            }),
                    ]),
            ]);
    }

    // Fixed: Updated method signature - should remain as Table
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->getStateUsing(fn ($record) =>
                        "https://nmsr.nickac.dev/bust/" . ($record->uuid ?? "00000000-0000-0000-0000-000000000000")
                    )
                    ->toggleable()
                    ->size(25),

                TextColumn::make('name')
                    ->weight('75%')
                    ->copyable()
                    ->copyMessage('Copied!')
                    ->copyMessageDuration(1500)
                    ->toggleable()
                    ->searchable()
                    ->copyableState(fn ($record): string => (string) $record->id),

                TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        $latest = $record->sessions->sortByDesc('session_start')->first();
                        return $latest && $latest->session_end === null ? 'Online' : 'Offline';
                    })
                    ->formatStateUsing(fn ($state) => $state === 'Online' ? '🟢 Online' : '⚪ Offline')
                    ->toggleable(),

                TextColumn::make('uuid')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('country_code')
                    ->label('Country')
                    ->getStateUsing(fn ($record) => $record->latestSession?->country_code ?? 'Unknown')
                    ->formatStateUsing(function ($state) {
                        return Cache::remember("country_flag_{$state}", now()->addMinutes(10), function () use ($state) {
                            return ($state !== 'Unknown'
                                ? self::countryFlagEmoji($state) . ' ' . strtoupper($state)
                                : 'Unknown');
                        });
                    })
                    ->toggleable(),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->primaryBadge['name'] ?? 'Member')
                    ->extraAttributes(fn ($record) => [
                        'class' => match($record->primaryBadge['color'] ?? 'slate') {
                            'red' => 'bg-red-500 text-white',
                            'black' => 'bg-black text-white',
                            'purple' => 'bg-purple-600 text-white',
                            'orange' => 'bg-orange-500 text-white',
                            'teal' => 'bg-teal-500 text-white',
                            'indigo' => 'bg-indigo-500 text-white',
                            'yellow' => 'bg-yellow-400 text-black',
                            'green' => 'bg-green-500 text-white',
                            'pink' => 'bg-pink-400 text-black',
                            'blue' => 'bg-blue-500 text-white',
                            'cyan' => 'bg-cyan-400 text-black',
                            default => 'bg-slate-500 text-white',
                        },
                    ]),

                TextColumn::make('created_at')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('online')
                    ->label('Online')
                    ->placeholder('All Players')
                    ->trueLabel('Online Only')
                    ->falseLabel('Offline Only')
                    ->queries(
                        true: fn ($query) => $query->whereHas('sessions', fn ($q) =>
                        $q->orderByDesc('session_start')->whereNull('session_end')
                        ),
                        false: fn ($query) => $query->whereHas('sessions', fn ($q) =>
                        $q->orderByDesc('session_start')->whereNotNull('session_end')
                        ),
                        blank: fn ($query) => $query,
                    ),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function query(): Builder
    {
        return Player::query()
            ->with(['permissions', 'discordConnection'])
            ->leftJoin('player_sessions', 'players.uuid', '=', 'player_sessions.uuid')
            ->orderByRaw('player_sessions.session_end IS NULL DESC')
            ->orderByDesc('player_sessions.session_start');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlayers::route('/'),
            'view' => Pages\ViewPlayer::route('/{record}'),
            'edit' => Pages\EditPlayer::route('/{record}/edit'),
        ];
    }

    public static function countryFlagEmoji(string $countryCode): string
    {
        if (!$countryCode || strlen($countryCode) !== 2) {
            return '';
        }

        return mb_convert_encoding('&#' . (127397 + ord($countryCode[0])) . ';', 'UTF-8', 'HTML-ENTITIES') .
            mb_convert_encoding('&#' . (127397 + ord($countryCode[1])) . ';', 'UTF-8', 'HTML-ENTITIES');
    }
}
