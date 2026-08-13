@props([
    'title',
    'value',
    'subtitle' => null,
    'icon' => 'heroicon-o-chart-bar',
    'color' => 'blue',
])

@php
    $colors = [
        'blue' => [
            'bg' => 'bg-blue-100',
            'text' => 'text-blue-600',
        ],
        'green' => [
            'bg' => 'bg-green-100',
            'text' => 'text-green-600',
        ],
        'red' => [
            'bg' => 'bg-red-100',
            'text' => 'text-red-600',
        ],
        'amber' => [
            'bg' => 'bg-amber-100',
            'text' => 'text-amber-600',
        ],
        'indigo' => [
            'bg' => 'bg-indigo-100',
            'text' => 'text-indigo-600',
        ],
        'orange' => [
            'bg' => 'bg-orange-100',
            'text' => 'text-orange-600',
        ],
    ];

    $style = $colors[$color] ?? $colors['blue'];
@endphp

<div
    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg">

    <div class="flex items-start justify-between">

        <div class="min-w-0">

            <p class="text-sm font-medium text-gray-500">
                {{ $title }}
            </p>

            <h2 class="mt-3 text-3xl font-bold tracking-tight text-gray-900">
                {{ $value }}
            </h2>

            @if($subtitle)
                <p class="mt-2 text-xs text-gray-400">
                    {{ $subtitle }}
                </p>
            @endif

        </div>

        <div
            class="flex h-14 w-14 items-center justify-center rounded-xl {{ $style['bg'] }}">

            <x-filament::icon
                :icon="$icon"
                class="h-7 w-7 {{ $style['text'] }}" />

        </div>

    </div>

</div>