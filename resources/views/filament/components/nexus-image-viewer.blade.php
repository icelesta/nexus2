@props([
    'image' => null,
    'title' => 'Image Preview',
])

<div
    x-data="{ open: false }"
    class="inline-block"
>

    {{-- Thumbnail --}}
    <img
        src="{{ $image }}"
        alt="{{ $title }}"
        class="cursor-pointer rounded-md transition hover:opacity-90"
        @click="open = true"
    >

    {{-- Modal --}}
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/80"
        style="display:none;"
    >

        {{-- Close --}}
        <button
            @click="open=false"
            class="absolute top-6 right-6 text-white text-3xl"
        >
            ✕
        </button>

        {{-- Image --}}
        <img
            src="{{ $image }}"
            alt="{{ $title }}"
            class="max-w-[90vw] max-h-[90vh] rounded-xl shadow-2xl"
        >

    </div>

</div>