<x-filament-panels::page>

    {{-- ========================================================= --}}
    {{-- ERP DOCUMENT HEADER --}}
    {{-- ========================================================= --}}
    <div class="rounded-xl border bg-white p-6 shadow-sm dark:bg-gray-900">

        <div class="flex items-start justify-between">

            <div>

                <h1 class="text-2xl font-bold">
                    Purchase Requisition
                </h1>

                <div class="mt-1 text-lg text-gray-600 dark:text-gray-300">
                    {{ $this->record->pr_no }}
                </div>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    View purchase requisition information, requested items,
                    approval status, and document history.
                </p>

            </div>

            <div
                class="flex items-center gap-5 rounded-xl border border-amber-300
                       bg-amber-300 px-8 py-6 shadow-sm">

                <div class="border-r border-amber-500 pr-5">

                    <x-heroicon-o-document-text
                        class="h-14 w-14 text-amber-800" />

                </div>

                <div>

                    <div
                        class="text-5xl font-extrabold tracking-wide text-amber-900">

                        {{ strtoupper($this->record->status) }}

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- GENERAL INFORMATION --}}
    {{-- ========================================================= --}}
    <div class="mt-6">

        {{ $this->infolist }}

    </div>

    {{-- ========================================================= --}}
    {{-- PURCHASE REQUISITION ITEMS --}}
    {{-- ========================================================= --}}
    <div class="mt-6 rounded-xl border bg-white shadow-sm dark:bg-gray-900">

        <div class="border-b px-6 py-4">

            <h2 class="text-lg font-semibold">

                Purchase Requisition Items

            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">

                List of requested items.

            </p>

        </div>

        <div class="space-y-6 p-6">

            {{-- Toolbar --}}
            @include('filament.resources.purchase-requisitions.pages.toolbar')

            {{-- ERP Item Grid --}}
            @include(
                'filament.resources.purchase-requisitions.pages.purchase-requisition-items-table-view'
            )
            
            @include('filament.resources.purchase-requisitions.pages.grid')

            {{-- ERP Summary --}}
            @include('filament.resources.purchase-requisitions.pages.summary')

        </div>

    </div>

</x-filament-panels::page>