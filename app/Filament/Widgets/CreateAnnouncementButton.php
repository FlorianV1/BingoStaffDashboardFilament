<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class CreateAnnouncementButton extends Widget
{
    protected static string $view = 'filament.widgets.create-announcement-button';

    public function getData(): array
    {
        return [];
    }
}
