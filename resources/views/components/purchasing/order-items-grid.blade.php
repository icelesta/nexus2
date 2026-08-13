<div>
<x-filament::section>

    <x-slot name="heading">
        Purchase Order Information
    </x-slot>

    <x-slot name="description">
        General information for this Purchase Order.
    </x-slot>



    {{-- ========================================================= --}}
    {{-- ERP Grid --}}
    {{-- ========================================================= --}}

    <div
        class="
            mt-5
            rounded-xl
            border
            border-gray-200
            bg-white
            shadow-sm
            overflow-hidden
        "
        wire:loading.class="opacity-60"
    >

        {{-- ===================================================== --}}
        {{-- Scroll Area --}}
        {{-- ===================================================== --}}

        <div
            class="
                overflow-y-auto
                overflow-x-auto
                max-h-[500px]
            "
        >

            <table
                class="
                    w-full
                    table-fixed
                    border-collapse
                    text-[12px]
                "
            >

                <thead
                    class="
                        sticky
                        top-0
                        z-30
                        bg-gray-100
                        shadow-sm
                    "
                >

                    <tr class="border-b text-[11px] font-semibold uppercase tracking-wide text-gray-700">

                        <th class="border-b bg-gray-100 px-2 py-2 text-center">
                            #
                        </th>

                        <th class="border-b bg-gray-100 px-2 py-2">
                            Item Code
                        </th>

                        <th class="border-b bg-gray-100 px-2 py-2">
                            Item Name
                        </th>

<!--                         <th class="border-b bg-gray-100 px-2 py-2">
                            Description
                        </th> -->

                        <th class="border-b bg-gray-100 px-2 py-2 text-center">
                            UOM
                        </th>

                        <th class="border-b bg-gray-100 px-2 py-2 text-center">
                            Qty
                        </th>

                        <th class="border-b bg-gray-100 px-2 py-2">
                            Supplier
                        </th>

                        <th class="border-b bg-gray-100 px-2 py-2 text-right">
                            Unit Price
                        </th>

                        <th class="border-b bg-gray-100 px-2 py-2 text-right">
                            Discount
                        </th>

                        <th class="border-b bg-gray-100 px-2 py-2">
                            Tax
                        </th>

                        <th class="border-b bg-gray-100 px-2 py-2 text-right">
                            Tax Amount
                        </th>

                        <th class="border-b bg-gray-100 px-2 py-2 text-right">
                            Amount
                        </th>

                        <th class="border-b bg-gray-100 px-2 py-2 text-center">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                @forelse ($items as $item)

                    @php

                        $statusColor = match ($item->status) {

                            'Open'                 => 'success',

                            'Partially Received'   => 'warning',

                            'Received'             => 'primary',

                            'Cancelled'            => 'danger',

                            default                => 'gray',

                        };

                    @endphp

                    <tr
                        wire:key="purchase-order-item-{{ $item->id }}"
                        class="transition-colors hover:bg-gray-50"
                    >

                        <td class="border px-2 py-2 text-center">
                            {{ $loop->iteration }}
                        </td>

                        <td class="border px-2 py-2">
                            {{ $item->item_code }}
                        </td>

                        <td class="border px-2 py-2">
                            {{ $item->item_name }}
                        </td>

<!--                         <td class="border px-2 py-2">
                            {{ $item->item_description ?: '-' }}
                        </td> -->

                        <td class="border px-2 py-2 text-center">
                            {{ $item->uom_name }}
                        </td>

                        <td class="border px-2 py-2 text-center">
                            {{ number_format((float) $item->ordered_qty, 2) }}
                        </td>

                        <td class="border px-2 py-2">
                            {{ $item->supplier?->supplier_name ?? '-' }}
                        </td>

                        <td class="border px-2 py-2 text-right">
                            Rp {{ number_format((float) $item->unit_price, 2) }}
                        </td>

                        <td class="border px-2 py-2 text-right">

                            {{ number_format((float) $item->discount_percent, 2) }} %

                            <br>

                            <span class="text-gray-500 text-[11px]">

                                Rp {{ number_format((float) $item->discount_amount, 2) }}

                            </span>

                        </td>

                        <td class="border px-2 py-2">
                            {{ $item->tax_name ?: '-' }}

                            @if($item->tax_percent)

                                <br>

                                <span class="text-gray-500 text-[11px]">

                                    {{ number_format((float)$item->tax_percent,2) }} %

                                </span>

                            @endif

                        </td>

                        <td class="border px-2 py-2 text-right">

                            Rp {{ number_format((float) $item->tax_amount, 2) }}

                        </td>

                        <td class="border px-2 py-2 text-right">

                            <span class="font-semibold text-blue-700">

                                Rp {{ number_format((float) $item->grand_total, 2) }}

                            </span>

                        </td>

                        <td class="border px-2 py-2 text-center">

                            <x-filament::badge
                                :color="$statusColor"
                            >

                                {{ $item->status }}

                            </x-filament::badge>

                        </td>

                    </tr>

                @empty

                <tr>

                    <td
                        colspan="12"
                        class="border py-20"
                    >

                        <div class="text-center">

                            <div
                                class="
                                    mx-auto
                                    flex
                                    h-20
                                    w-20
                                    items-center
                                    justify-center
                                    rounded-full
                                    bg-gray-100
                                "
                            >

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="h-10 w-10 text-gray-400"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M20.25 7.5v10.125A2.625 2.625 0 0117.625 20.25H6.375A2.625 2.625 0 013.75 17.625V7.5m16.5 0L18.75 4.875A2.625 2.625 0 0016.125 3H7.875A2.625 2.625 0 005.25 4.875L3.75 7.5m16.5 0H3.75"
                                    />

                                </svg>

                            </div>

                            <div
                                class="
                                    mt-5
                                    text-lg
                                    font-semibold
                                    text-gray-900
                                "
                            >
                                No Purchase Order Items
                            </div>

                            <div
                                class="
                                    mt-2
                                    text-xs
                                    text-gray-500
                                "
                            >
                                There are no items available in this Purchase Order.
                            </div>

                        </div>

                    </td>

                </tr>

                @endforelse

                </tbody>                    

                <tfoot>

                    <tr>

                        <td colspan="12" class="h-14 border-0 bg-transparent">
                        </td>

                    </tr>

                </tfoot>

                </table>

                </div>
                {{-- End Scroll Area --}}

                {{-- ========================================================= --}}
                {{-- ERP Footer Summary --}}
                {{-- ========================================================= --}}

                <div
                    class="
                        border-t
                        border-gray-200
                        bg-gray-50
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            px-5
                            py-4
                            text-xs
                        "
                    >

                        {{-- Left --}}

                        <div class="flex items-center gap-8">

                            <div>

                                <span class="text-gray-500">

                                    Total Lines :

                                </span>

                                <span
                                    style="
                                        color:#1d4ed8;
                                        font-weight:bold;
                                    "
                                >

                                    {{ $this->totalItems }}

                                </span>

                            </div>

                            <div>

                                <span class="text-gray-500">

                                    Ordered Qty :

                                </span>

                                <span
                                    style="
                                        color:#1d4ed8;
                                        font-weight:bold;
                                    "
                                >

                                    {{ number_format($this->totalOrderedQty,2) }}

                                </span>

                            </div>

                        </div>

                        {{-- Right Summary --}}

                        <div
                            class="
                                flex
                                items-center
                                gap-8
                                pr-8
                            "
                        >

                            <div>

                                <span class="text-gray-500">

                                    Gross :

                                </span>

                                <span
                                    style="
                                        color:#1d4ed8;
                                        font-weight:bold;
                                    "
                                >

                                    Rp {{ number_format($this->totalGrossAmount,2) }}

                                </span>

                            </div>

                            <div>

                                <span class="text-gray-500">

                                    Discount :

                                </span>

                                <span
                                    style="
                                        color:#dc2626;
                                        font-weight:bold;
                                    "
                                >

                                    Rp {{ number_format($this->totalDiscountAmount,2) }}

                                </span>

                            </div>

                            <div>

                                <span class="text-gray-500">

                                    Tax :

                                </span>

                                <span
                                    style="
                                        color:#2563eb;
                                        font-weight:bold;
                                    "
                                >

                                    Rp {{ number_format($this->totalTaxAmount,2) }}

                                </span>

                            </div>

                            <div
                                class="
                                    border-l
                                    pl-6
                                "
                            >

                                <span class="text-gray-500">

                                    Grand Total :

                                </span>

                                <span
                                    class="
                                        text-base
                                        font-bold
                                    "
                                    style="color:#1d4ed8;"
                                >

                                    Rp {{ number_format($this->grandTotal,2) }}

                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- Close ERP Grid --}}
                </div>

                </x-filament::section>
                </div>                