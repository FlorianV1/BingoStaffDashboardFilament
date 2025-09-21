@php
    $options = [
        'all_online' => 'All Online',
        'all_lobby' => 'Lobby',
        'player' => 'Player',
    ];
@endphp

<div
    x-data="{
        value: @entangle('formData.recipientType').defer,
        setValue(val) {
            this.value = val;
        }
    }"
    class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full text-white"
>
    @foreach ($options as $value => $label)
        <button
            type="button"
            @click="setValue(@js($value))"
            :class="value === @js($value)
                ? 'bg-primary-600 border-primary-700 shadow-xl'
                : 'bg-gray-800 border-gray-600 hover:border-primary-500 hover:bg-gray-700'"
            class="p-6 rounded-xl border-2 transition-all text-xl font-bold text-center w-full"
        >
            {{ $label }}
        </button>
    @endforeach
</div>
