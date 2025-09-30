<?php

namespace App\Filament\Pages;

use App\Models\PlayerAnnouncement;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Notifications\Notification;
use BackedEnum;
class CreateAnnouncement extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Create Announcement';
    protected static ?string $slug = 'create-announcement';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Wizard::make([
                Step::make('Type')
                    ->schema([
                        Section::make('Message Type')
                            ->schema([
                                View::make('form.prefix-selector'),
                            ]),
                    ]),

                Step::make('Sent to')
                    ->schema([
                        Section::make('Send To')
                            ->schema([
                                View::make('form.recipient-selector'),

                                TextInput::make('player')
                                    ->label('Player UUID')
                                    ->visible(fn ($get) => $get('recipientType') === 'player')
                                    ->required(fn ($get) => $get('recipientType') === 'player'),
                            ]),
                    ]),

                Step::make('Message')
                    ->schema([
                        Section::make('Chat Message')
                            ->schema([
                                View::make('form.chat-colors'),

                                TextInput::make('message')
                                    ->label('Chat')
                                    ->placeholder('Type your message here...')
                                    ->required()
                                    ->maxLength(255)
                                    ->extraAttributes(['x-ref' => 'chatInput']),
                            ]),
                    ]),
                ])
                    ->submitAction(
                        Action::make('send')
                            ->label('Send Announcement')
                            ->submit('send')
                    ),
            ])
            ->statePath('data');
    }

    public function send(): void
    {
        $data = $this->form->getState();

        PlayerAnnouncement::create([
            'player_uuid' => ($data['recipientType'] ?? null) === 'player' ? ($data['player'] ?? null) : null,
            'message' => ($data['prefix'] ?? '') . ' ' . ($data['message'] ?? ''),
            'is_sent' => false,
        ]);

        Notification::make()
            ->title('Announcement created successfully.')
            ->success()
            ->send();

        $this->form->fill();
    }

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        return parent::getUrl($parameters, $isAbsolute, $panel, $tenant);
    }
}
