<?php

namespace App\Filament\Resources\BingoServerResource\Pages;

use App\Filament\Resources\BingoServerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBingoServer extends EditRecord
{
    protected static string $resource = BingoServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
