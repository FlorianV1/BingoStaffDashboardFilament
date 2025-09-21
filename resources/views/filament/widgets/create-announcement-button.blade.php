<x-filament::card>
    <div class="space-y-2">
        <h2 class="text-lg font-bold">Announcements</h2>

        <x-filament::button
            tag="a"
            href="{{ route('filament.admin.pages.create-announcement') }}"
            icon="heroicon-o-megaphone"
        >
            Create Announcement
        </x-filament::button>
    </div>
</x-filament::card>
