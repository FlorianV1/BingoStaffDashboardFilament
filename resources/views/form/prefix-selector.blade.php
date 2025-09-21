@php
    $options = [
        '&l&7[&l&aBingoMC&l&7]&7' => 'BingoMC',
        '&7&l[&c&lAlert&7&l]&7' => 'Alert',
        '&7&l[&b&lEvent&7&l]&7' => 'Event',
    ];
@endphp

<div
    x-data="{
        value: @entangle('formData.prefix').defer,
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
