<?php

namespace App\Filament\Resources\Chatlogs\Pages;

use App\Filament\Resources\Chatlogs\Chatlogs\ChatlogsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateChatlogs extends CreateRecord
{
    protected static string $resource = ChatlogsResource::class;
}
