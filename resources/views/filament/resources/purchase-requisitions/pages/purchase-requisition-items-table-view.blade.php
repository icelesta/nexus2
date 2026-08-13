@php

    $items = $record?->items ?? collect();

    $summary = [

        'total_lines' => $items->count(),

        'total_qty' => $items->sum('quantity'),

        'estimated_amount' => $items->sum(
            fn ($item) => $item->quantity * $item->estimated_unit_price
        ),

    ];

@endphp

@php
    /** @var \App\Filament\Resources\PurchaseRequisitions\Pages\EditPurchaseRequisition $livewire */
    $livewire = $__livewire;
@endphp




<div class="space-y-6">

{{-- ============================================================= --}}
{{-- Toolbar (Read Only) --}}
{{-- ============================================================= --}}

    <div class="flex flex-wrap items-center justify-between gap-3">

        <div class="flex flex-wrap gap-2">

            <x-filament::button
                color="warning"
                icon="heroicon-m-plus"
                disabled>
                Add Item
            </x-filament::button>

            <x-filament::button
                color="gray"
                icon="heroicon-m-arrow-up-tray"
                disabled>
                Import Excel
            </x-filament::button>

            <x-filament::button
                color="gray"
                icon="heroicon-m-document-duplicate"
                disabled>
                Copy From PR
            </x-filament::button>

            <x-filament::button
                color="danger"
                icon="heroicon-m-trash"
                disabled>
                Clear
            </x-filament::button>

        </div>

    </div>

    {{-- ============================================================= --}}
    {{-- Item Table --}}
    {{-- ============================================================= --}}

    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

                <thead class="
                    bg-gray-50
                    dark:bg-gray-800
                    border-b
                    text-xs
                    uppercase
                    tracking-wider
                    font-semibold
                    text-gray-700
                    dark:text-gray-300
                ">

                    <tr class="
                        hover:bg-gray-50
                        dark:hover:bg-gray-800
                        transition-colors
                    ">

                        <th class="w-10 px-4 py-3">
                            <input type="checkbox">
                        </th>

                        <th class="w-10 px-4 py-3 text-center">#</th>

                        <th class="w-30 px-4 py-3 text-left">ITEM CODE</th>

                        <th class="w-72 px-4 py-3 text-left">ITEM NAME</th>


                        <th class="w-[30rem] px-4 py-3 text-left">DESCRIPTION</th>


                        <th class="w-24 px-4 py-3 text-right">QTY</th>

                        <th class="w-24 px-4 py-3 text-center">UOM</th>

                        <th class="w-40 px-4 py-3 text-center">REQUIRED DATE</th>

                        <th class="w-64 px-4 py-3 text-left">WAREHOUSE</th>

<!--                         <th class="w-28 px-4 py-3 text-center">STATUS</th> -->

                        <th class="w-24 px-4 py-3 text-center">ACTION</th>

                    </tr>

                </thead>

                 <tbody class="bg-white dark:bg-gray-900">

                    @if($items->isEmpty())

                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-gray-500">
                                No material items.
                            </td>
                        </tr>

                    @else

                        @foreach($items as $index => $item)

                            <tr class="{{ $loop->even
                                ? 'bg-gray-50 dark:bg-gray-800/40'
                                : 'bg-white dark:bg-gray-900'
                            }}">

                                <td class="px-4 py-3 align-middle"></td>

                                <td class="px-4 py-3 align-middle">
                                    {{ $index + 1 }}
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    {{ $item->item?->item_code }}
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    {{ $item->item?->item_name }}
                                </td>

                                {{-- DESCRIPTION --}}
                                <td class="px-4 py-3 align-middle">
                                    {{ $item->remarks }}
                                </td>

                                {{-- QTY --}}
                                <td class="px-4 py-3 text-center align-middle">
                                    {{ number_format($item->quantity, 2) }}
                                </td>

                                <td class="px-4 py-3 text-center align-middle">
                                    {{ $item->uom?->uom_name }}
                                </td>

                                <td class="px-4 py-3 text-center align-middle">
                                    {{ $item->required_date?->format('d-M-Y') }}
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    {{ $item->warehouse?->warehouse_name }}
                                </td>

                                {{-- ACTION (READ ONLY) --}}
                                <td class="px-4 py-3 text-center align-middle">

                                    <span class="text-gray-400 font-medium">
                                        —
                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    @endif

                </tbody>

            </table>

        </div>

        {{-- ========================================================= --}}
        {{-- Footer --}}
        {{-- ========================================================= --}}

        <div class="flex items-center justify-between border-t bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800">

            <div class="text-sm text-gray-600 dark:text-gray-300">

                <strong>Total Lines :</strong>

                {{ number_format($summary['total_lines'] ?? 0) }}

            </div>

            <div class="flex items-center gap-8 text-sm">

                <div>

                    <strong>Total Qty :</strong>

                    {{ number_format($summary['total_qty'] ?? 0, 2) }}

                </div>

                <div>

                    <strong>Estimated Amount :</strong>

                    Rp {{ number_format($summary['estimated_amount'] ?? 0, 2) }}

                </div>

            </div>

        </div>


    </div>

</div>