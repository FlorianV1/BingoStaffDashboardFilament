<?php

namespace App\Filament\Resources\Chatlogs\Pages;

use App\Filament\Resources\Chatlogs\Chatlogs\ChatlogsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChatlogs extends EditRecord
{
    protected static string $resource = ChatlogsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
