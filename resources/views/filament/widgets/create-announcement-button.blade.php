<x-filament-widgets::widget>
    <x-filament::section
        heading="Announcements"
        description="Create and manage player announcements"
    >
        @if($canCreateAnnouncement)
            <x-filament::button
                tag="a"
                href="{{ $announcementUrl }}"
                icon="heroicon-o-megaphone"
                color="primary"
            >
                Create Announcement
            </x-filament::button>
        @else
            <x-filament::badge color="warning">
                You don't have permission to create announcements
            </x-filament::badge>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
