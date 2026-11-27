<x-filament::page>
    @php
        $hasActiveFilters = $this->hasActiveFilters();

        $formatQty = static function ($value): string {
            return rtrim(rtrim(number_format((float) $value, 4, '.', ','), '0'), '.');
        };
    @endphp

    {{-- ========================================================= --}}
    {{-- KPI SUMMARY --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-blue-600">
                        Outstanding Lines
                    </p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        {{ number_format($totalLines) }}
                    </p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50">
                    <x-filament::icon
                        icon="heroicon-o-clipboard-document-list"
                        class="h-6 w-6 text-blue-600"
                    />
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-amber-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-amber-600">
                        Total Outstanding Qty
                    </p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $formatQty($totalOutstanding) }}
                    </p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50">
                    <x-filament::icon
                        icon="heroicon-o-cube"
                        class="h-6 w-6 text-amber-600"
                    />
                </div>
            </div>
            <p class="mt-1 text-xs text-slate-400">
                Combined quantity across different UOMs
            </p>
        </div>

        <div class="rounded-xl border border-violet-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-violet-600">
                        Source Documents
                    </p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        {{ number_format($totalDocuments) }}
                    </p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50">
                    <x-filament::icon
                        icon="heroicon-o-document-text"
                        class="h-6 w-6 text-violet-600"
                    />
                </div>
            </div>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- TRANSACTION FILTERS --}}
    {{-- ========================================================= --}}

    <div class="mt-5 w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        {{-- TOP ACCENT --}}
        <div class="h-1 w-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

        <div class="px-5 pb-5 pt-4">

            {{-- HEADER --}}
            <div class="mb-5 flex items-start justify-between gap-3">

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
                            Refine outstanding item data
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <span class="h-2 w-2 rounded-full
                        {{ $hasActiveFilters
                            ? 'bg-emerald-500 shadow-sm shadow-emerald-200'
                            : 'bg-slate-300' }}">
                    </span>

                    <span class="text-xs font-semibold
                        {{ $hasActiveFilters
                            ? 'text-emerald-600'
                            : 'text-slate-500' }}">
                        {{ $hasActiveFilters ? 'Filters Active' : 'No Filters Applied' }}
                    </span>
                </div>

            </div>


            {{-- FILTER GRID --}}
            <div class="grid grid-cols-1 items-end gap-3 sm:grid-cols-2 lg:grid-cols-7">

                {{-- BRANCH --}}
                <div class="min-w-0">
                    <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-blue-600">
                        <x-filament::icon icon="heroicon-o-building-office-2" class="h-4 w-4 text-blue-500" />
                        Branch
                    </label>

                    <select
                        wire:model.live="branchFilter"
                        class="block w-full min-w-0 rounded-xl border-blue-100 px-3 py-2 pr-8 text-sm font-medium shadow-sm transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100
                        {{ $branchFilter !== null ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-100' : 'bg-blue-50/60 text-slate-700' }}"
                    >
                        <option value="">All Branch</option>
                        @foreach ($this->branchOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>


                {{-- DEPARTMENT --}}
                <div class="min-w-0">
                    <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-violet-600">
                        <x-filament::icon icon="heroicon-o-briefcase" class="h-4 w-4 text-violet-500" />
                        Department
                    </label>

                    <select
                        wire:model.live="departmentFilter"
                        class="block w-full min-w-0 rounded-xl border-violet-100 px-3 py-2 pr-8 text-sm font-medium shadow-sm transition focus:border-violet-500 focus:bg-white focus:ring-2 focus:ring-violet-100
                        {{ $departmentFilter !== null ? 'bg-violet-50 text-violet-700 ring-1 ring-violet-100' : 'bg-violet-50/60 text-slate-700' }}"
                    >
                        <option value="">All Department</option>
                        @foreach ($this->departmentOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>


                {{-- SOURCE --}}
                <div class="min-w-0">
                    <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-indigo-600">
                        <x-filament::icon icon="heroicon-o-rectangle-stack" class="h-4 w-4 text-indigo-500" />
                        Source
                    </label>

                    <select
                        wire:model.live="sourceType"
                        class="block w-full min-w-0 rounded-xl border-indigo-100 px-3 py-2 pr-8 text-sm font-medium shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100
                        {{ $sourceType !== 'ALL' ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100' : 'bg-indigo-50/60 text-slate-700' }}"
                    >
                        <option value="ALL">All Source</option>
                        <option value="PO">Purchase Order</option>
                        <option value="DM">Direct Market</option>
                    </select>
                </div>


                {{-- SUPPLIER --}}
                <div class="min-w-0">
                    <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-cyan-600">
                        <x-filament::icon icon="heroicon-o-truck" class="h-4 w-4 text-cyan-500" />
                        Supplier
                    </label>

                    <select
                        wire:model.live="supplierFilter"
                        class="block w-full min-w-0 rounded-xl border-cyan-100 px-3 py-2 pr-8 text-sm font-medium shadow-sm transition focus:border-cyan-500 focus:bg-white focus:ring-2 focus:ring-cyan-100
                        {{ $supplierFilter !== null ? 'bg-cyan-50 text-cyan-700 ring-1 ring-cyan-100' : 'bg-cyan-50/60 text-slate-700' }}"
                    >
                        <option value="">All Suppliers</option>
                        @foreach ($this->supplierOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>


                {{-- FROM --}}
                <div class="min-w-0">
                    <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-emerald-600">
                        <x-filament::icon icon="heroicon-o-calendar" class="h-4 w-4 text-emerald-500" />
                        From
                    </label>

                    <input
                        type="date"
                        wire:model.live="dateFrom"
                        class="block w-full min-w-0 rounded-xl border-emerald-100 px-3 py-2 text-sm font-medium shadow-sm transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100
                        {{ filled($dateFrom) ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-emerald-50/60 text-slate-700' }}"
                    >
                </div>


                {{-- TO --}}
                <div class="min-w-0">
                    <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-emerald-600">
                        <x-filament::icon icon="heroicon-o-calendar" class="h-4 w-4 text-emerald-500" />
                        To
                    </label>

                    <input
                        type="date"
                        wire:model.live="dateTo"
                        class="block w-full min-w-0 rounded-xl border-emerald-100 px-3 py-2 text-sm font-medium shadow-sm transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100
                        {{ filled($dateTo) ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-emerald-50/60 text-slate-700' }}"
                    >
                </div>


                {{-- RESET --}}
                <div class="flex min-w-0 items-end">
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600 focus:border-blue-300 focus:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                        <x-filament::icon
                            icon="heroicon-o-arrow-path"
                            class="h-4 w-4 text-slate-400"
                        />
                        Reset
                    </button>
                </div>

            </div>


            {{-- SEARCH --}}
            <div class="mt-5 flex justify-end">
                <div class="w-full sm:w-80">
                    <label class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-slate-600">
                        <x-filament::icon icon="heroicon-o-magnifying-glass" class="h-4 w-4 text-slate-500" />
                        Search
                    </label>

                    <div class="relative">
                        <input
                            type="search"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Document, item code, description, supplier..."
                            class="block w-full rounded-xl border-slate-200 bg-slate-50 py-2 pl-3 pr-10 text-sm font-medium text-slate-700 shadow-sm transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                        >

                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <x-filament::icon
                                icon="heroicon-o-magnifying-glass"
                                class="h-4 w-4 text-slate-400"
                            />
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- OUTSTANDING ITEMS TABLE --}}
    {{-- ========================================================= --}}

    <div class="mt-5 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-900">
                    Outstanding Item Details
                </h3>
                <p class="mt-1 text-xs text-slate-500">
                    Item lines with remaining quantity available for receiving
                </p>
            </div>

            <div class="flex items-center gap-2">
                <label for="outstanding-per-page" class="text-xs font-medium text-slate-500">
                    Show
                </label>

                <select
                    id="outstanding-per-page"
                    wire:model.live="perPage"
                    class="rounded-lg border-slate-200 bg-white py-1.5 pl-2 pr-7 text-xs font-semibold text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-100"
                >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">All</option>
                </select>

                <span class="text-xs text-slate-500">entries</span>
            </div>
        </div>


        <div class="overflow-x-auto">
            <table class="w-full min-w-[1150px] divide-y divide-gray-200 text-sm">

                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Source
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Document No.
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Date
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Supplier
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Item Code
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            Item Description
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                            Source Qty
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                            Received
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                            Outstanding
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            UOM
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">

                    @forelse ($rows as $row)
                        <tr class="transition hover:bg-slate-50">

                            <td class="whitespace-nowrap px-4 py-3">
                                @if ($row['source_type'] === 'PO')
                                    <x-filament::badge color="info">
                                        Purchase Order
                                    </x-filament::badge>
                                @else
                                    <x-filament::badge color="primary">
                                        Direct Market
                                    </x-filament::badge>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-700">
                                {{ $row['source_no'] }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                {{ $row['source_date']
                                    ? \Illuminate\Support\Carbon::parse($row['source_date'])->format('d M Y')
                                    : '-' }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $row['supplier'] }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-700">
                                {{ $row['item_code'] }}
                            </td>

                            <td class="min-w-[220px] px-4 py-3 text-slate-600">
                                {{ $row['item_name'] }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-slate-600">
                                {{ $formatQty($row['source_qty']) }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-slate-600">
                                {{ $formatQty($row['received_qty']) }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <span class="font-bold tabular-nums text-amber-700">
                                    {{ $formatQty($row['outstanding_qty']) }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-slate-500">
                                {{ $row['uom'] }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                <a
                                    href="{{ \App\Filament\Resources\GoodsReceipts\GoodsReceiptResource::getUrl('create', [
                                        'supplier_id' => $row['supplier_id'],
                                        'source_type' => $row['source_type'] === 'PO'
                                            ? 'PURCHASE_ORDER'
                                            : 'ASSIGNMENT_DIRECT_MARKET',
                                    ]) }}"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-300"
                                >
                                    <x-filament::icon
                                        icon="heroicon-o-plus"
                                        class="h-3.5 w-3.5"
                                    />
                                    Create RR
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <x-filament::icon
                                        icon="heroicon-o-clipboard-document-list"
                                        class="h-10 w-10 text-slate-300"
                                    />
                                    <p class="mt-3 text-sm font-semibold text-slate-600">
                                        No outstanding items found
                                    </p>
                                    <p class="mt-1 text-xs text-slate-400">
                                        Try adjusting your filters or search criteria
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>


        {{-- PAGINATION --}}
        <div class="border-t border-gray-100 px-5 py-4">
            {{ $rows->links() }}
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- INFORMATION --}}
    {{-- ========================================================= --}}

    <div class="flex items-start gap-2 rounded-xl border border-blue-100 bg-blue-50/60 px-4 py-3">
        <x-filament::icon
            icon="heroicon-o-information-circle"
            class="mt-0.5 h-5 w-5 shrink-0 text-blue-600"
        />

        <p class="text-xs leading-relaxed text-slate-600">
            This page is for monitoring purposes only. Outstanding quantities are calculated
            from approved PO and Direct Market items, less submitted receiving quantities.
            Items reserved in a Draft Receiving Record are temporarily hidden until the draft
            is submitted or cancelled.
        </p>
    </div>

</x-filament::page>