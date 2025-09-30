<?php

namespace App\Filament\Resources\BingoServers\Pages;

use App\Filament\Resources\BingoServers\BingoServers\BingoServerResource;
use Filament\Resources\Pages\ListRecords;

class ListBingoServers extends ListRecords
{
    protected static string $resource = BingoServerResource::class;

    protected function canCreate(): bool
    {
        return false;
    }
}
