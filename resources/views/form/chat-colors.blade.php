@php
    $colors = [
        '&0' => '#000000',
        '&1' => '#0000AA',
        '&2' => '#00AA00',
        '&3' => '#00AAAA',
        '&4' => '#AA0000',
        '&5' => '#AA00AA',
        '&6' => '#FFAA00',
        '&7' => '#AAAAAA',
        '&8' => '#555555',
        '&9' => '#5555FF',
        '&a' => '#55FF55',
        '&b' => '#55FFFF',
        '&c' => '#FF5555',
        '&d' => '#FF55FF',
        '&e' => '#FFFF55',
        '&f' => '#FFFFFF',
    ];
@endphp

<div
    x-data="{
        message: @entangle('formData.message').defer,
        addColor(code) {
            this.message += code;
            $nextTick(() => $refs.chatInput?.focus());
        }
    }"
    class="space-y-2"
>
    <label class="block text-sm font-semibold text-white">Chat Color</label>
    <div class="flex flex-wrap gap-2">
        @foreach ($colors as $code => $hex)
            <button
                type="button"
                class="w-8 h-8 rounded-full border-2 border-gray-700 hover:border-primary-500 transition-all focus:outline-none"
                style="background-color: {{ $hex }};"
                title="{{ $code }}"
                @click="addColor(@js($code))"
            ></button>
        @endforeach
    </div>
</div>
