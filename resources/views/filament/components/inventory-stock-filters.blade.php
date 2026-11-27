<div class="relative z-50 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-visible">

    <div class="h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

    <div class="p-4">

        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <x-heroicon-o-funnel class="w-5 h-5 text-primary-600" />

                <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                    Transaction Filters
                </h3>
            </div>

            <span class="text-xs text-gray-500">
                Inventory Stock Filters
            </span>
        </div>

        {{ $livewire->getTableFiltersForm() }}

    </div>
</div>