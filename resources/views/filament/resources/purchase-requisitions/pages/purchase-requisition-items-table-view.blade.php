@php

    $items = $record?->items ?? collect();

    $summary = [

        'total_lines' => $items->count(),

        'total_qty' => $items->sum('quantity'),

        'estimated_amount' => $items->sum(
            fn ($item) =>
                $item->quantity * $item->estimated_unit_price
        ),

    ];

@endphp


<div class="space-y-6">

    {{-- ============================================================= --}}
    {{-- Item Table - GOLDEN READ ONLY --}}
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

                    <tr>

                        <th class="w-10 px-4 py-3 text-center">
                            #
                        </th>

                        <th class="w-30 px-4 py-3 text-left">
                            ITEM CODE
                        </th>

                        <th class="w-72 px-4 py-3 text-left">
                            ITEM NAME
                        </th>

                        <th class="w-[30rem] px-4 py-3 text-left">
                            DESCRIPTION
                        </th>

                        <th class="w-24 px-4 py-3 text-right">
                            QTY
                        </th>

                        <th class="w-24 px-4 py-3 text-center">
                            UOM
                        </th>

                        <th class="w-40 px-4 py-3 text-center">
                            REQUIRED DATE
                        </th>

                        <th class="w-64 px-4 py-3 text-left">
                            WAREHOUSE
                        </th>

                        <th class="w-64 px-4 py-3 text-left">
                            SUPPLIER
                        </th>

                        <th class="w-32 px-4 py-3 text-right">
                            UNIT PRICE
                        </th>

                        <th class="w-28 px-4 py-3 text-right">
                            DISCOUNT
                        </th>

                        <th class="w-28 px-4 py-3 text-right">
                            TAX
                        </th>

                        <th class="w-32 px-4 py-3 text-right">
                            TAX AMOUNT
                        </th>

                        <th class="w-32 px-4 py-3 text-right">
                            AMOUNT
                        </th>

                    </tr>

                </thead>


                <tbody class="bg-white dark:bg-gray-900">

                    @if($items->isEmpty())

                        <tr>

                            <td
                                colspan="15"
                                class="px-6 py-10 text-center text-gray-500"
                            >
                                No material items.
                            </td>

                        </tr>

                    @else

                        @foreach($items as $index => $item)

                            @php
                                $assignmentItem =
                                    $item->latestAssignmentMaterialRequisitionItem;
                            @endphp

                            <tr class="
                                {{ $loop->even
                                    ? 'bg-gray-50 dark:bg-gray-800/40'
                                    : 'bg-white dark:bg-gray-900'
                                }}
                            ">

                                {{-- # --}}
                                <td class="px-4 py-3 text-center align-middle">
                                    {{ $index + 1 }}
                                </td>


                                {{-- ITEM CODE --}}
                                <td class="px-4 py-3 align-middle">
                                    {{ $item->item?->item_code ?? '-' }}
                                </td>


                                {{-- ITEM NAME --}}
                                <td class="px-4 py-3 align-middle">
                                    {{ $item->item?->item_name ?? '-' }}
                                </td>


                                {{-- DESCRIPTION --}}
                                <td class="px-4 py-3 align-middle">
                                    {{ $item->remarks ?? '-' }}
                                </td>


                                {{-- QTY --}}
                                <td class="px-4 py-3 text-right align-middle">
                                    {{ number_format($item->quantity, 2) }}
                                </td>


                                {{-- UOM --}}
                                <td class="px-4 py-3 text-center align-middle">
                                    {{ $item->uom?->uom_name ?? '-' }}
                                </td>


                                {{-- REQUIRED DATE --}}
                                <td class="px-4 py-3 text-center align-middle">
                                    {{ $item->required_date?->format('d-M-Y') ?? '-' }}
                                </td>


                                {{-- WAREHOUSE --}}
                                <td class="px-4 py-3 align-middle">
                                    {{ $item->warehouse?->warehouse_name ?? '-' }}
                                </td>


                                {{-- SUPPLIER --}}
                                <td class="px-4 py-3 align-middle">
                                    {{ $assignmentItem?->supplier?->name ?? '-' }}
                                </td>


                                {{-- UNIT PRICE --}}
                                <td class="px-4 py-3 text-right align-middle">

                                    @if($assignmentItem)

                                        Rp
                                        {{ number_format(
                                            $assignmentItem->unit_price ?? 0,
                                            2
                                        ) }}

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- DISCOUNT --}}
                                <td class="px-4 py-3 text-right align-middle">

                                    @if($assignmentItem)

                                        {{ number_format(
                                            $assignmentItem->discount_percent ?? 0,
                                            2
                                        ) }}%

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- TAX --}}
                                <td class="px-4 py-3 text-right align-middle">

                                    @if($assignmentItem)

                                        {{ $assignmentItem->tax_name ?? '-' }}

                                        <div class="text-xs text-gray-500">

                                            {{ number_format(
                                                $assignmentItem->tax_percent ?? 0,
                                                2
                                            ) }}%

                                        </div>

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- TAX AMOUNT --}}
                                <td class="px-4 py-3 text-right align-middle">

                                    @if($assignmentItem)

                                        Rp
                                        {{ number_format(
                                            $assignmentItem->tax_amount ?? 0,
                                            2
                                        ) }}

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- AMOUNT --}}
                                <td class="px-4 py-3 text-right align-middle">

                                    @if($assignmentItem)

                                        Rp
                                        {{ number_format(
                                            $assignmentItem->grand_total ?? 0,
                                            2
                                        ) }}

                                    @else

                                        -

                                    @endif

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

        <div class="
            flex
            items-center
            justify-between
            border-t
            bg-gray-50
            px-6
            py-4
            dark:border-gray-700
            dark:bg-gray-800
        ">

            <div class="text-sm text-gray-600 dark:text-gray-300">

                <strong>Total Lines :</strong>

                {{ number_format($summary['total_lines'] ?? 0) }}

            </div>


            <div class="flex items-center gap-8 text-sm">

                <div>

                    <strong>Total Qty :</strong>

                    {{ number_format(
                        $summary['total_qty'] ?? 0,
                        2
                    ) }}

                </div>


                <div>

                    <strong>Commercial Amount :</strong>

                    Rp
                    {{ number_format(
                        $items->sum(
                            fn ($item) =>
                                $item
                                    ->latestAssignmentMaterialRequisitionItem
                                    ?->grand_total ?? 0
                        ),
                        2
                    ) }}

                </div>

            </div>

        </div>

    </div>

</div>