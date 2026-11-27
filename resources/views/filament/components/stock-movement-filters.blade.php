@if ($livewire->canUseGlobalTransactionFilters())

    <div class="w-full bg-white">
        <div class="border-t border-gray-200">

            {{-- TOP ACCENT --}}
            <div class="h-1 w-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

            <div class="px-5 pb-5 pt-4">

                {{-- HEADER --}}
                <div class="mb-4 flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 ring-1 ring-blue-100">
                            <x-filament::icon
                                icon="heroicon-o-funnel"
                                class="h-5 w-5 text-blue-600"
                            />
                        </div>

                        <div>
                            <div class="text-sm font-semibold tracking-tight text-slate-900">
                                Transaction Filters
                            </div>
                            <div class="text-xs font-medium text-slate-500">
                                Refine stock movement history
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <span class="h-2 w-2 rounded-full
                            {{ $livewire->hasActiveStockMovementFilters()
                                ? 'bg-emerald-500 shadow-sm shadow-emerald-200'
                                : 'bg-slate-300' }}">
                        </span>

                        <span class="text-xs font-semibold
                            {{ $livewire->hasActiveStockMovementFilters()
                                ? 'text-emerald-600'
                                : 'text-slate-500' }}">
                            {{ $livewire->hasActiveStockMovementFilters()
                                ? 'Filters Active'
                                : 'No Filters Applied' }}
                        </span>
                    </div>
                </div>

                {{-- FILTER GRID --}}
                <div class="grid min-w-0 grid-cols-1 items-end gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">

                    {{-- MOVEMENT TYPE --}}
                    <div class="min-w-0">
                        <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-blue-600">
                            <x-filament::icon icon="heroicon-o-arrows-right-left" class="h-4 w-4 text-blue-500"/>
                            Movement Type
                        </label>

                        <select wire:model="movementTypeFilter"
                            class="block w-full min-w-0 rounded-xl border-blue-100 bg-blue-50/60 px-3 py-2 text-sm font-medium text-slate-700 shadow-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                            <option value="">All Types</option>
                            @foreach ($livewire->movementTypeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- WAREHOUSE --}}
                    <div class="min-w-0">
                        <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-violet-600">
                            <x-filament::icon icon="heroicon-o-building-office-2" class="h-4 w-4 text-violet-500"/>
                            Warehouse
                        </label>

                        <select wire:model="movementWarehouseFilter"
                            class="block w-full min-w-0 rounded-xl border-violet-100 bg-violet-50/60 px-3 py-2 text-sm font-medium text-slate-700 shadow-sm focus:border-violet-500 focus:bg-white focus:ring-2 focus:ring-violet-100">
                            <option value="">All Warehouses</option>
                            @foreach ($livewire->movementWarehouseOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ITEM --}}
                    <div class="min-w-0">
                        <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-amber-600">
                            <x-filament::icon icon="heroicon-o-cube" class="h-4 w-4 text-amber-500"/>
                            Item
                        </label>

                        <select wire:model="movementItemFilter"
                            class="block w-full min-w-0 rounded-xl border-amber-100 bg-amber-50/60 px-3 py-2 text-sm font-medium text-slate-700 shadow-sm focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-100">
                            <option value="">All Items</option>
                            @foreach ($livewire->movementItemOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- CREATED BY --}}
                    <div class="min-w-0">
                        <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-cyan-600">
                            <x-filament::icon icon="heroicon-o-user" class="h-4 w-4 text-cyan-500"/>
                            Created By
                        </label>

                        <select wire:model="movementCreatorFilter"
                            class="block w-full min-w-0 rounded-xl border-cyan-100 bg-cyan-50/60 px-3 py-2 text-sm font-medium text-slate-700 shadow-sm focus:border-cyan-500 focus:bg-white focus:ring-2 focus:ring-cyan-100">
                            <option value="">All Users</option>
                            @foreach ($livewire->movementCreatorOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FROM DATE --}}
                    <div class="min-w-0">
                        <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-emerald-600">
                            <x-filament::icon icon="heroicon-o-calendar" class="h-4 w-4 text-emerald-500"/>
                            From Date
                        </label>

                        <input type="date"
                            wire:model="movementDateFrom"
                            class="block w-full min-w-0 rounded-xl border-emerald-100 bg-emerald-50/60 px-3 py-2 text-sm font-medium text-slate-700 shadow-sm focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100">
                    </div>

                    {{-- TO DATE --}}
                    <div class="min-w-0">
                        <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-emerald-600">
                            <x-filament::icon icon="heroicon-o-calendar" class="h-4 w-4 text-emerald-500"/>
                            To Date
                        </label>

                        <input type="date"
                            wire:model="movementDateTo"
                            min="{{ $livewire->movementDateFrom }}"
                            class="block w-full min-w-0 rounded-xl border-emerald-100 bg-emerald-50/60 px-3 py-2 text-sm font-medium text-slate-700 shadow-sm focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100">
                    </div>

                    {{-- REFERENCE --}}
                    <div class="min-w-0">
                        <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-rose-600">
                            <x-filament::icon icon="heroicon-o-document-text" class="h-4 w-4 text-rose-500"/>
                            Reference Number
                        </label>

                        <input type="text"
                            wire:model="movementReferenceFilter"
                            placeholder="Search RR Number"
                            class="block w-full min-w-0 rounded-xl border-rose-100 bg-rose-50/60 px-3 py-2 text-sm font-medium text-slate-700 shadow-sm focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-100">
                    </div>

                </div>
            </div>
        </div>
    </div>

@endif