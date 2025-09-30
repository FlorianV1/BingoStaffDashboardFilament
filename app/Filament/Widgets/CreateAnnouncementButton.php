<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\CreateAnnouncement;
use Filament\Widgets\Widget;


class CreateAnnouncementButton extends Widget
{
    protected string $view = 'filament.widgets.create-announcement-button';
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    /**
     * Get the data to pass to the widget view
     */
    protected function getViewData(): array
    {
        return [
            'announcementUrl' => CreateAnnouncement::getUrl(),
            'canCreateAnnouncement' => $this->canCreate(),
        ];
    }

    /**
     * Check if the current user can create announcements
     */
    protected function canCreate(): bool
    {
        return true;
    }
}
