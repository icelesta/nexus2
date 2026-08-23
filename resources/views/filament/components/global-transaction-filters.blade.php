@if ($livewire->canUseGlobalTransactionFilters())

    <div class="w-full bg-white">

        {{-- ========================================================= --}}
        {{-- TRANSACTION FILTER PANEL --}}
        {{-- ========================================================= --}}

        <div class="border-t border-gray-200">

            {{-- ========================================================= --}}
            {{-- TOP ACCENT --}}
            {{-- ========================================================= --}}

            <div class="h-1 w-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>


            <div class="px-5 pb-5 pt-4">

                {{-- ===================================================== --}}
                {{-- HEADER --}}
                {{-- ===================================================== --}}

                <div class="mb-4 flex items-start justify-between">

                    <div class="flex items-center gap-3">

                        {{-- Filter Icon --}}
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl
                                   bg-blue-50 ring-1 ring-blue-100"
                        >

                            <x-filament::icon
                                icon="heroicon-o-funnel"
                                class="h-5 w-5 text-blue-600"
                            />

                        </div>


                        {{-- Header Text --}}
                        <div>

                            <div
                                class="text-sm font-semibold tracking-tight text-slate-900"
                            >
                                Transaction Filters
                            </div>

                            <div
                                class="text-xs font-medium text-slate-500"
                            >
                                Refine transaction data
                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- ACTIVE INDICATOR --}}
                    {{-- ================================================= --}}

                    <div class="flex items-center gap-2 pt-1">

                        <span
                            class="h-2 w-2 rounded-full
                                {{
                                    $livewire->hasActiveGlobalTransactionFilters()
                                        ? 'bg-emerald-500 shadow-sm shadow-emerald-200'
                                        : 'bg-slate-300'
                                }}"
                        ></span>

                        <span
                            class="text-xs font-semibold
                                {{
                                    $livewire->hasActiveGlobalTransactionFilters()
                                        ? 'text-emerald-600'
                                        : 'text-slate-500'
                                }}"
                        >
                            {{
                                $livewire->hasActiveGlobalTransactionFilters()
                                    ? 'Filters Active'
                                    : 'No Filters Applied'
                            }}
                        </span>

                    </div>

                </div>


                {{-- ===================================================== --}}
                {{-- FILTER GRID --}}
                {{-- ===================================================== --}}

                <div
                    class="grid min-w-0 items-end gap-3
                        {{
                            $livewire instanceof \App\Filament\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders
                                ? 'grid-cols-[2.2fr_2.2fr_1.7fr_1.4fr_1.25fr_1.25fr_0.9fr]'
                                : 'grid-cols-[2.5fr_2.5fr_1.7fr_1.5fr_1.5fr_0.9fr]'
                        }}"
                >


                    {{-- ================================================= --}}
                    {{-- BRANCH --}}
                    {{-- ================================================= --}}

                    <div class="min-w-0">

                        <label
                            class="mb-2 flex items-center gap-2
                                   text-xs font-bold uppercase
                                   tracking-wide text-blue-600"
                        >

                            <x-filament::icon
                                icon="heroicon-o-building-office-2"
                                class="h-4 w-4 text-blue-500"
                            />

                            Branch

                        </label>


                        <div class="relative">

                            <select
                                wire:model.live="globalBranchFilter"
                                class="block w-full min-w-0 rounded-xl
                                       border-blue-100
                                       {{
                                           $livewire->globalBranchFilter !== null
                                               ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-100'
                                               : 'bg-blue-50/60 text-slate-700'
                                       }}
                                       px-3 py-2
                                       pr-10
                                       text-sm
                                       font-medium
                                       shadow-sm
                                       transition
                                       hover:border-blue-300
                                       hover:bg-blue-50
                                       focus:border-blue-500
                                       focus:bg-white
                                       focus:ring-2
                                       focus:ring-blue-100"
                            >

                                <option value="">
                                    All Branch
                                </option>

                                @foreach ($livewire->globalBranchOptions as $id => $name)

                                    <option value="{{ $id }}">
                                        {{ $name }}
                                    </option>

                                @endforeach

                            </select>


                            <div
                                class="pointer-events-none absolute inset-y-0 right-3
                                       flex items-center"
                            >

                                <x-filament::icon
                                    icon="heroicon-m-chevron-down"
                                    class="h-4 w-4 text-blue-400"
                                />

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- DEPARTMENT --}}
                    {{-- ================================================= --}}

                    <div class="min-w-0">

                        <label
                            class="mb-2 flex items-center gap-2
                                   text-xs font-bold uppercase
                                   tracking-wide text-violet-600"
                        >

                            <x-filament::icon
                                icon="heroicon-o-briefcase"
                                class="h-4 w-4 text-violet-500"
                            />

                            Department

                        </label>


                        <div class="relative">

                            <select
                                wire:model.live="globalDepartmentFilter"
                                class="block w-full min-w-0 rounded-xl
                                       border-violet-100
                                       {{
                                           $livewire->globalDepartmentFilter !== null
                                               ? 'bg-violet-50 text-violet-700 ring-1 ring-violet-100'
                                               : 'bg-violet-50/60 text-slate-700'
                                       }}
                                       px-3 py-2
                                       pr-10
                                       text-sm
                                       font-medium
                                       shadow-sm
                                       transition
                                       hover:border-violet-300
                                       hover:bg-violet-50
                                       focus:border-violet-500
                                       focus:bg-white
                                       focus:ring-2
                                       focus:ring-violet-100"
                            >

                                <option value="">
                                    All Department
                                </option>

                                @foreach ($livewire->globalDepartmentOptions as $id => $name)

                                    <option value="{{ $id }}">
                                        {{ $name }}
                                    </option>

                                @endforeach

                            </select>


                            <div
                                class="pointer-events-none absolute inset-y-0 right-3
                                       flex items-center"
                            >

                                <x-filament::icon
                                    icon="heroicon-m-chevron-down"
                                    class="h-4 w-4 text-violet-400"
                                />

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- SUPPLIER — PO ONLY --}}
                    {{-- ================================================= --}}

                    @if (
                        $livewire instanceof \App\Filament\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders
                    )

                        <div class="min-w-0">

                            <label
                                class="mb-2 flex items-center gap-2
                                       text-xs font-bold uppercase
                                       tracking-wide text-cyan-600"
                            >

                                <x-filament::icon
                                    icon="heroicon-o-truck"
                                    class="h-4 w-4 text-cyan-500"
                                />

                                Supplier

                            </label>


                            <div class="relative">

                                <select
                                    wire:model.live="globalSupplierFilter"
                                    class="block w-full min-w-0 rounded-xl
                                           border-cyan-100
                                           {{
                                               $livewire->globalSupplierFilter !== null
                                                   ? 'bg-cyan-50 text-cyan-700 ring-1 ring-cyan-100'
                                                   : 'bg-cyan-50/60 text-slate-700'
                                           }}
                                           px-3 py-2
                                           pr-10
                                           text-sm
                                           font-medium
                                           shadow-sm
                                           transition
                                           hover:border-cyan-300
                                           hover:bg-cyan-50
                                           focus:border-cyan-500
                                           focus:bg-white
                                           focus:ring-2
                                           focus:ring-cyan-100"
                                >

                                    <option value="">
                                        All Suppliers
                                    </option>

                                    @foreach ($livewire->globalSupplierOptions as $id => $name)

                                        <option value="{{ $id }}">
                                            {{ $name }}
                                        </option>

                                    @endforeach

                                </select>


                                <div
                                    class="pointer-events-none absolute inset-y-0 right-3
                                           flex items-center"
                                >

                                    <x-filament::icon
                                        icon="heroicon-m-chevron-down"
                                        class="h-4 w-4 text-cyan-400"
                                    />

                                </div>

                            </div>

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- STATUS --}}
                    {{-- ================================================= --}}

                    <div class="min-w-0">

                        <label
                            class="mb-2 flex items-center gap-2
                                   text-xs font-bold uppercase
                                   tracking-wide text-amber-600"
                        >

                            <x-filament::icon
                                icon="heroicon-o-flag"
                                class="h-4 w-4 text-amber-500"
                            />

                            Status

                        </label>


                        <div class="relative">

                            <select
                                wire:model.live="globalStatusFilter"
                                class="block w-full min-w-0 rounded-xl
                                       border-amber-100
                                       {{
                                           filled($livewire->globalStatusFilter)
                                               ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-100'
                                               : 'bg-amber-50/60 text-slate-700'
                                       }}
                                       px-3 py-2
                                       pr-10
                                       text-sm
                                       font-medium
                                       shadow-sm
                                       transition
                                       hover:border-amber-300
                                       hover:bg-amber-50
                                       focus:border-amber-500
                                       focus:bg-white
                                       focus:ring-2
                                       focus:ring-amber-100"
                            >

                                <option value="">
                                    All Status
                                </option>

                                @foreach ($livewire->getGlobalTransactionStatusOptions() as $value => $label)

                                    <option value="{{ $value }}">
                                        {{ $label }}
                                    </option>

                                @endforeach

                            </select>


                            <div
                                class="pointer-events-none absolute inset-y-0 right-3
                                       flex items-center"
                            >

                                <x-filament::icon
                                    icon="heroicon-m-chevron-down"
                                    class="h-4 w-4 text-amber-400"
                                />

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- FROM --}}
                    {{-- ================================================= --}}

                    <div class="min-w-0">

                        <label
                            class="mb-2 flex items-center gap-2
                                   text-xs font-bold uppercase
                                   tracking-wide text-emerald-600"
                        >

                            <x-filament::icon
                                icon="heroicon-o-calendar"
                                class="h-4 w-4 text-emerald-500"
                            />

                            From

                        </label>


                        <input
                            type="date"
                            wire:model.live="globalDateFrom"
                            class="block w-full min-w-0 rounded-xl
                                   border-emerald-100
                                   {{
                                       filled($livewire->globalDateFrom)
                                           ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100'
                                           : 'bg-emerald-50/60 text-slate-700'
                                   }}
                                   px-3 py-2
                                   text-sm
                                   font-medium
                                   shadow-sm
                                   transition
                                   hover:border-emerald-300
                                   hover:bg-emerald-50
                                   focus:border-emerald-500
                                   focus:bg-white
                                   focus:ring-2
                                   focus:ring-emerald-100"
                        >

                    </div>


                    {{-- ================================================= --}}
                    {{-- TO --}}
                    {{-- ================================================= --}}

                    <div class="min-w-0">

                        <label
                            class="mb-2 flex items-center gap-2
                                   text-xs font-bold uppercase
                                   tracking-wide text-emerald-600"
                        >

                            <x-filament::icon
                                icon="heroicon-o-calendar"
                                class="h-4 w-4 text-emerald-500"
                            />

                            To

                        </label>


                        <input
                            type="date"
                            wire:model.live="globalDateTo"
                            class="block w-full min-w-0 rounded-xl
                                   border-emerald-100
                                   {{
                                       filled($livewire->globalDateTo)
                                           ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100'
                                           : 'bg-emerald-50/60 text-slate-700'
                                   }}
                                   px-3 py-2
                                   text-sm
                                   font-medium
                                   shadow-sm
                                   transition
                                   hover:border-emerald-300
                                   hover:bg-emerald-50
                                   focus:border-emerald-500
                                   focus:bg-white
                                   focus:ring-2
                                   focus:ring-emerald-100"
                        >

                    </div>


                    {{-- ================================================= --}}
                    {{-- RESET --}}
                    {{-- ================================================= --}}

                    <div class="flex min-w-0 items-end">

                        <button
                            type="button"
                            wire:click="resetGlobalTransactionFilters"
                            class="flex w-full items-center justify-center gap-2
                                   rounded-xl
                                   border border-slate-200
                                   bg-white
                                   px-3 py-2
                                   text-sm
                                   font-semibold
                                   text-slate-600
                                   shadow-sm
                                   transition
                                   hover:border-blue-200
                                   hover:bg-blue-50
                                   hover:text-blue-600
                                   focus:border-blue-300
                                   focus:bg-blue-50
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-blue-100"
                        >

                            <x-filament::icon
                                icon="heroicon-o-arrow-path"
                                class="h-4 w-4 text-slate-400"
                            />

                            <span>
                                Reset
                            </span>

                        </button>

                    </div>


                </div>

            </div>

        </div>

    </div>

@endif