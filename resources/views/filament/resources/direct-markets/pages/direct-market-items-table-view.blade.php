@php

    /*
    |--------------------------------------------------------------------------
    | DIRECT MARKET ITEMS
    |--------------------------------------------------------------------------
    |
    | Base data:
    |   Direct Market Item
    |
    | Commercial snapshot:
    |   Assignment Direct Market Item
    |
    | Commercial snapshot is displayed ONLY after ADM final approval
    | (Approval 2/2).
    |
    */

    $items = $record?->items ?? collect();

    $items->loadMissing([
        'item',
        'uom',
    ]);

    /*
    |--------------------------------------------------------------------------
    | RESOLVE FINAL ADM APPROVAL
    |--------------------------------------------------------------------------
    */

    $assignment = \App\Models\AssignmentDirectMarket::query()
        ->with([
            'items.directMarketItem',
            'items.item',
            'items.uom',
            'items.supplier',
            'items.tax',
        ])
        ->where(
            'direct_market_id',
            $record?->getKey()
        )
        ->latest('id')
        ->first();

    $approvalTransaction = $assignment
    ? \App\Models\ApprovalTransaction::query()
        ->where(
            'document_type',
            'ASSIGNMENT_DIRECT_MARKET'
        )
        ->where(
            'document_id',
            $assignment->getKey()
        )
        ->latest('id')
        ->first()
    : null;

    $admApproved =
        $assignment !== null
        && $assignment->status === 'Approved'
        && $approvalTransaction?->status === 'APPROVED';

    /*
    |--------------------------------------------------------------------------
    | MAP ADM ITEMS
    |--------------------------------------------------------------------------
    |
    | direct_market_item_id is the bridge between DM item
    | and ADM item.
    |
    */

    $assignmentItems = $assignment?->items
        ?->keyBy('direct_market_item_id')
        ?? collect();

    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    $summary = [

        'total_lines' =>
            $items->count(),

        'total_qty' =>
            $items->sum(
                fn ($item): float =>
                    (float) $item->qty
            ),

        'commercial_amount' =>
            $admApproved
                ? $assignmentItems->sum(
                    fn ($assignmentItem): float =>
                        (float) $assignmentItem->grand_total
                )
                : 0,

    ];

@endphp


<div class="space-y-6">

    {{-- ============================================================= --}}
    {{-- Direct Market Items Table --}}
    {{-- ============================================================= --}}

    <div
        class="
            overflow-hidden
            rounded-xl
            border
            border-gray-200
            dark:border-gray-700
        "
    >

        <div class="overflow-x-auto">

            <table
                class="
                    min-w-full
                    divide-y
                    divide-gray-200
                    dark:divide-gray-700
                "
            >

                <thead
                    class="
                        bg-gray-50
                        dark:bg-gray-800
                        border-b
                        text-xs
                        uppercase
                        tracking-wider
                        font-semibold
                        text-gray-700
                        dark:text-gray-300
                    "
                >

                    <tr>

                        {{-- # --}}
                        <th class="w-10 px-4 py-3 text-center">
                            #
                        </th>

                        {{-- ITEM CODE --}}
                        <th class="w-32 px-4 py-3 text-left">
                            ITEM CODE
                        </th>

                        {{-- ITEM NAME --}}
                        <th class="w-56 px-4 py-3 text-left">
                            ITEM NAME
                        </th>

                        {{-- ITEM REMARK --}}
                        <th class="w-64 px-4 py-3 text-left">
                            ITEM REMARK
                        </th>

                        {{-- UOM --}}
                        <th class="w-20 px-4 py-3 text-center">
                            UOM
                        </th>

                        {{-- REQUESTED QTY --}}
                        <th class="w-28 px-4 py-3 text-center">
                            REQUESTED QTY
                        </th>

                        @if($admApproved)

                            {{-- ASSIGNED QTY --}}
                            <th class="w-28 px-4 py-3 text-center">
                                ASSIGNED QTY
                            </th>

                            {{-- SUPPLIER --}}
                            <th class="w-56 px-4 py-3 text-left">
                                SUPPLIER
                            </th>

                            {{-- UNIT PRICE --}}
                            <th class="w-36 px-4 py-3 text-right">
                                UNIT PRICE
                            </th>

                            {{-- DISC % --}}
                            <th class="w-24 px-4 py-3 text-right">
                                DISC %
                            </th>

                            {{-- DISC AMT --}}
                            <th class="w-36 px-4 py-3 text-right">
                                DISC AMT
                            </th>

                            {{-- AMOUNT --}}
                            <th class="w-40 px-4 py-3 text-right">
                                AMOUNT
                            </th>

                        @else

                            {{-- REQUIRED DATE --}}
                            <th class="w-40 px-4 py-3 text-center">
                                REQUIRED DATE
                            </th>


                        @endif

                    </tr>

                </thead>


                <tbody
                    class="
                        bg-white
                        dark:bg-gray-900
                    "
                >

                    @if($items->isEmpty())

                        <tr>

                            <td
                                colspan="{{ $admApproved ? 12 : 8 }}"
                                class="
                                    px-6
                                    py-10
                                    text-center
                                    text-gray-500
                                "
                            >
                                No Direct Market items.
                            </td>

                        </tr>

                    @else

                        @foreach($items as $index => $item)

                            @php

                                /*
                                |--------------------------------------------------------------------------
                                | ADM SNAPSHOT
                                |--------------------------------------------------------------------------
                                */

                                $assignmentItem =
                                    $assignmentItems->get(
                                        $item->getKey()
                                    );

                            @endphp


                            <tr
                                class="
                                    {{
                                        $loop->even
                                            ? 'bg-gray-50 dark:bg-gray-800/40'
                                            : 'bg-white dark:bg-gray-900'
                                    }}
                                "
                            >

                                {{-- # --}}
                                <td
                                    class="
                                        px-4
                                        py-3
                                        text-center
                                        align-middle
                                    "
                                >
                                    {{ $index + 1 }}
                                </td>


                                {{-- ITEM CODE --}}
                                <td
                                    class="
                                        px-4
                                        py-3
                                        align-middle
                                    "
                                >
                                    {{
                                        $item->item?->item_code
                                        ?? '-'
                                    }}
                                </td>


                                {{-- ITEM NAME --}}
                                <td
                                    class="
                                        px-4
                                        py-3
                                        align-middle
                                        font-medium
                                    "
                                >
                                    {{
                                        $item->item?->item_name
                                        ?? '-'
                                    }}
                                </td>


                                {{-- ITEM REMARK --}}
                                <td
                                    class="
                                        px-4
                                        py-3
                                        align-middle
                                    "
                                >
                                    {{
                                        $item->remarks
                                        ?? '-'
                                    }}
                                </td>


                                {{-- UOM --}}
                                <td
                                    class="
                                        px-4
                                        py-3
                                        text-center
                                        align-middle
                                    "
                                >
                                    {{
                                        $item->uom?->uom_code
                                        ?? $item->uom?->uom_name
                                        ?? '-'
                                    }}
                                </td>


                                {{-- REQUESTED QTY --}}
                                <td
                                    class="
                                        px-4
                                        py-3
                                        text-center
                                        align-middle
                                    "
                                >
                                    {{ number_format(
                                        (float) $item->qty,
                                        2
                                    ) }}
                                </td>


                                @if($admApproved)

                                    {{-- ASSIGNED QTY --}}
                                    <td
                                        class="
                                            px-4
                                            py-3
                                            text-center
                                            align-middle
                                        "
                                    >
                                        {{ number_format(
                                            (float) (
                                                $assignmentItem?->assigned_qty
                                                ?? 0
                                            ),
                                            2
                                        ) }}
                                    </td>


                                    {{-- SUPPLIER --}}
                                    <td
                                        class="
                                            px-4
                                            py-3
                                            align-middle
                                        "
                                    >
                                        {{
                                            $assignmentItem
                                                ?->supplier
                                                ?->supplier_name
                                            ?? '-'
                                        }}
                                    </td>


                                    {{-- UNIT PRICE --}}
                                    <td
                                        class="
                                            px-4
                                            py-3
                                            text-right
                                            align-middle
                                        "
                                    >
                                        Rp
                                        {{ number_format(
                                            (float) (
                                                $assignmentItem
                                                    ?->unit_price
                                                ?? 0
                                            ),
                                            2,
                                            ',',
                                            '.'
                                        ) }}
                                    </td>


                                    {{-- DISC % --}}
                                    <td
                                        class="
                                            px-4
                                            py-3
                                            text-right
                                            align-middle
                                        "
                                    >
                                        {{ number_format(
                                            (float) (
                                                $assignmentItem
                                                    ?->discount_percent
                                                ?? 0
                                            ),
                                            2,
                                            ',',
                                            '.'
                                        ) }}%
                                    </td>


                                    {{-- DISC AMT --}}
                                    <td
                                        class="
                                            px-4
                                            py-3
                                            text-right
                                            align-middle
                                        "
                                    >
                                        Rp
                                        {{ number_format(
                                            (float) (
                                                $assignmentItem
                                                    ?->discount_amount
                                                ?? 0
                                            ),
                                            2,
                                            ',',
                                            '.'
                                        ) }}
                                    </td>


                                    {{-- AMOUNT --}}
                                    <td
                                        class="
                                            px-4
                                            py-3
                                            text-right
                                            align-middle
                                            font-semibold
                                        "
                                    >
                                        Rp
                                        {{ number_format(
                                            (float) (
                                                $assignmentItem
                                                    ?->grand_total
                                                ?? 0
                                            ),
                                            2,
                                            ',',
                                            '.'
                                        ) }}
                                    </td>

                                @else

                                    {{-- REQUIRED DATE --}}
                                    <td
                                        class="
                                            px-4
                                            py-3
                                            text-center
                                            align-middle
                                        "
                                    >
                                        {{
                                            $item->required_date
                                                ?->format('d-M-Y')
                                            ?? '-'
                                        }}
                                    </td>


                                @endif

                            </tr>

                        @endforeach

                    @endif

                </tbody>

            </table>

        </div>


        {{-- ========================================================= --}}
        {{-- Footer --}}
        {{-- ========================================================= --}}

        <div
            class="
                flex
                flex-wrap
                items-center
                justify-between
                gap-4
                border-t
                bg-gray-50
                px-6
                py-4
                dark:border-gray-700
                dark:bg-gray-800
            "
        >

            <div
                class="
                    text-sm
                    text-gray-600
                    dark:text-gray-300
                "
            >

                <strong>Total Lines :</strong>

                {{ number_format(
                    $summary['total_lines'] ?? 0
                ) }}

            </div>


            <div
                class="
                    flex
                    flex-wrap
                    items-center
                    gap-8
                    text-sm
                "
            >

                <div>

                    <strong>Total Qty :</strong>

                    {{ number_format(
                        $summary['total_qty'] ?? 0,
                        2
                    ) }}

                </div>


                @if($admApproved)

                    <div>

                        <strong>Commercial Amount :</strong>

                        <span
                            class="
                                font-semibold
                                text-primary-600
                                dark:text-primary-400
                            "
                        >
                            Rp
                            {{ number_format(
                                $summary['commercial_amount'] ?? 0,
                                2,
                                ',',
                                '.'
                            ) }}
                        </span>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>