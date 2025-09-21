<div class="space-y-4">
    @if (!$server)
        <div class="text-red-600 font-semibold">Server not found.</div>
    @else
        <div>
            <h2 class="text-xl font-bold">Server #{{ $server->id }}</h2>
            <p><strong>IP:</strong> {{ $server->ip }}:{{ $server->port }}</p>
            <p><strong>Variant:</strong> {{ $server->variant }}</p>
            <p><strong>State:</strong> {{ $server->state }}</p>
            <p><strong>Join Code:</strong> {{ $server->join_code }}</p>
        </div>

        <div>
            <h2 class ="mt-4 text-lg font-semibold">Players</h2>
            <p><strong></strong></p>
        </div>
    @endif
</div>
