<?php

namespace App\Filament\Resources\Players\Pages;

use App\Filament\Resources\Players\Players\PlayerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePlayer extends CreateRecord
{
    protected static string $resource = PlayerResource::class;
}
