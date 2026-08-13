@php
    $user = auth()->user();
@endphp

@if ($user)

    <div
        class="flex items-center border-l border-gray-200 pl-4 pr-2 dark:border-gray-700"
    >

        <span
            class="whitespace-nowrap text-sm font-semibold text-gray-800 dark:text-gray-200"
        >
            {{ $user->name }}
        </span>

    </div>

@endif