@php
    $refererPath = parse_url(
        request()->header('referer', ''),
        PHP_URL_PATH
    ) ?? '';

    $isInventoryStockPage =
        request()->routeIs('filament.admin.resources.inventory-stocks.index')
        || str_contains($refererPath, '/admin/inventory-stocks');
@endphp

@if ($isInventoryStockPage)
    <div class="flex flex-wrap items-center gap-2">

        <x-filament::button
            color="gray"
            size="sm"
            outlined
            icon="heroicon-o-arrow-path"
            wire:click="resetTableFiltersForm"
        >
            Reset
        </x-filament::button>

        <x-filament::button
            color="primary"
            size="sm"
            icon="heroicon-o-funnel"
            wire:click="applyTableFilters"
        >
            Apply Filters
        </x-filament::button>

    </div>
@endif