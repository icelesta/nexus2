@php
    $refererPath = parse_url(
        request()->header('referer', ''),
        PHP_URL_PATH
    ) ?? '';

    $isStockMovementPage =
        request()->routeIs('filament.admin.resources.inventory-stock-movements.index')
        || str_contains($refererPath, '/admin/inventory-stock-movements');
@endphp

@if ($isStockMovementPage)
    <div class="flex flex-wrap items-center gap-2">

        <x-filament::button
            color="gray"
            size="sm"
            outlined
            icon="heroicon-o-arrow-path"
            wire:click="resetStockMovementFilters"
        >
            Reset
        </x-filament::button>

        <x-filament::button
            color="primary"
            size="sm"
            icon="heroicon-o-funnel"
            wire:click="applyStockMovementFilters"
        >
            Apply Filters
        </x-filament::button>

    </div>
@endif