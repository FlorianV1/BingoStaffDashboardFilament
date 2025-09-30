<?php

namespace App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\PlayerResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewPlayer extends ViewRecord
{
    protected static string $resource = PlayerResource::class;

    public int $chatPage = 1;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Edit')
                ->url(fn () => PlayerResource::getUrl('edit', ['record' => $this->record])),
        ];
    }
}
