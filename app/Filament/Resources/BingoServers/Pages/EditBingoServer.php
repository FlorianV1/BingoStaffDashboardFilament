<?php

namespace App\Filament\Resources\BingoServers\Pages;

use App\Filament\Resources\BingoServers\BingoServers\BingoServerResource;
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
