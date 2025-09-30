<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-2">
            <h2 class="text-lg font-bold">Announcements</h2>

            <x-filament::button
                tag="a"
                href="{{ \App\Filament\Pages\CreateAnnouncement::getUrl() }}"
                icon="heroicon-o-megaphone"
            >
                Create Announcement
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
