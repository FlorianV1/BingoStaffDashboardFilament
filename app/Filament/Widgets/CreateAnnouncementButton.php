<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class CreateAnnouncementButton extends Widget
{
    protected string $view = 'filament.widgets.create-announcement-button';

    // Fixed: Updated method visibility and type hint for v4 compatibility
    protected function getData(): array
    {
        return [];
    }
}
