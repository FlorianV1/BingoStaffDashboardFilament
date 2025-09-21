<?php

namespace App\Filament\Pages;

use App\Models\PlayerAnnouncement;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\ButtonAction;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\View;

class CreateAnnouncement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = NULL;
    protected static string $view = 'filament.pages.create-announcement';
    protected static ?string $slug = 'create-announcement';
//    protected static ?string $navigationGroup = 'Other';

    public array $formData = [];

    public function mount(): void
    {
        $this->form->fill($this->formData);
    }

    protected function getFormSchema(): array
    {
        return [
            Wizard::make([
                Step::make('Type')
                    ->schema([
                        Card::make([
                            \Filament\Forms\Components\View::make('form.section-heading')
                                ->viewData(['text' => 'Message Type']),

                            \Filament\Forms\Components\View::make('form.prefix-selector'),
                        ])->columnSpanFull(),
                    ]),
                Step::make('Sent to')
                    ->schema([
                        Card::make([
                            \Filament\Forms\Components\View::make('form.section-heading')
                                ->viewData(['text' => 'Send To']),

                            \Filament\Forms\Components\View::make('form.recipient-selector'),

                            TextInput::make('player')
                                ->statePath('formData.player')
                                ->label('Player UUID')
                                ->visible(fn ($get) => $get('formData.recipientType') === 'player')
                                ->required(fn ($get) => $get('formData.recipientType') === 'player'),
                        ])->columnSpanFull(),
                    ]),

                Step::make('Message')
                    ->schema([
                        Card::make([
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
                    ButtonAction::make('send')
                        ->label('Send Announcement')
                        ->action('send')
                ),
        ];
    }
    public function send(): void
    {
        $data = $this->form->getState();

        PlayerAnnouncement::create([
            'player_uuid' => $data['recipientType'] === 'player' ? $data['player'] : null,
            'message' => $data['prefix'] . ' ' . $data['message'],
            'is_sent' => false,
        ]);

        $this->notify('success', 'Announcement created.');
        $this->form->fill([]);
    }
}
