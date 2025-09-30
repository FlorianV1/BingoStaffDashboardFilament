<?php

namespace App\Filament\Pages;

use App\Models\PlayerAnnouncement;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Column;
use Filament\Notifications\Notification;
class CreateAnnouncement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|null|\backedenum $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = null;
    protected string $view = 'filament.pages.create-announcement';
    protected static ?string $slug = 'create-announcement';

    public array $formData = [];

    public function mount(): void
    {
        $this->form->fill($this->formData);
    }

    protected function getFormSchema(): Schema
    {
        return Schema::make()
            ->components([
                Wizard::make([
                    Step::make('Type')
                        ->schema([
                            Section::make()
                                ->schema([
                                    View::make('form.section-heading')
                                        ->viewData(['text' => 'Message Type']),

                                    View::make('form.prefix-selector'),
                                ])
                                ->columnSpanFull(),
                        ]),
                    Step::make('Sent to')
                        ->schema([
                            Section::make()
                                ->schema([
                                    View::make('form.section-heading')
                                        ->viewData(['text' => 'Send To']),

                                    View::make('form.recipient-selector'),

                                    TextInput::make('player')
                                        ->statePath('formData.player')
                                        ->label('Player UUID')
                                        ->visible(fn ($get) => $get('formData.recipientType') === 'player')
                                        ->required(fn ($get) => $get('formData.recipientType') === 'player'),
                                ])
                                ->columnSpanFull(),
                        ]),

                    Step::make('Message')
                        ->schema([
                            Section::make()
                                ->schema([
                                    View::make('form.section-heading')
                                        ->viewData(['text' => 'Chat Message']),

                                    View::make('form.chat-colors'),

                                    TextInput::make('message')
                                        ->statePath('formData.message')
                                        ->label('Chat')
                                        ->placeholder('Type your message here...')
                                        ->required()
                                        ->maxLength(255)
                                        ->extraAttributes(['x-ref' => 'chatInput']), // link x-ref to Blade view
                                ]),
                        ]),
                ])
                    ->submitAction(
                        Action::make('send')
                            ->label('Send Announcement')
                            ->action('send')
                    ),
            ]);
    }

    public function send(): void
    {
        $data = $this->form->getState();

        PlayerAnnouncement::create([
            'player_uuid' => $data['recipientType'] === 'player' ? $data['player'] : null,
            'message' => $data['prefix'] . ' ' . $data['message'],
            'is_sent' => false,
        ]);

        // Fixed: Updated notification syntax for v4
        Notification::make()
            ->title('Announcement created.')
            ->success()
            ->send();

        $this->form->fill([]);
    }
}
