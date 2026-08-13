<div>
<x-filament::section>

    <x-slot name="heading">
        Assignment Items
    </x-slot>

    @if ($this->canEdit)

    <x-slot name="description">
        Assign supplier, quantity and pricing for each material.
    </x-slot>

    @endif


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
                <colgroup>
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

                        <th class="
                                    border-b
                                    bg-gray-100
                                    px-2
                                    py-2
                                    font-semibold
                                    uppercase
                                    tracking-wide
                                    text-gray-700
                                    ">
                            #
                        </th>

                        <th class="
                                    border-b
                                    bg-gray-100
                                    px-2
                                    py-2
                                    font-semibold
                                    uppercase
                                    tracking-wide
                                    text-gray-700
                                    ">
                            Item Code
                        </th>

                        <th class="
                                    border-b
                                    bg-gray-100
                                    px-2
                                    py-2
                                    font-semibold
                                    uppercase
                                    tracking-wide
                                    text-gray-700
                                    ">
                            Item Name
                        </th>

                        <th
                            class="
                                border-b
                                bg-gray-100
                                px-2
                                py-2
                                font-semibold
                                uppercase
                                tracking-wide
                                text-gray-700
                            "
                        >
                            Description
                        </th>                    

                        <th class="
                                    border-b
                                    bg-gray-100
                                    px-2
                                    py-2
                                    font-semibold
                                    uppercase
                                    tracking-wide
                                    text-gray-700
                                    ">
                            UOM
                        </th>

                        <th class="
                                    border-b
                                    bg-gray-100
                                    px-2
                                    py-2
                                    font-semibold
                                    uppercase
                                    tracking-wide
                                    text-gray-700
                                    ">
                            Requested
                        </th>

                        <th class="
                                    border-b
                                    bg-gray-100
                                    px-2
                                    py-2
                                    font-semibold
                                    uppercase
                                    tracking-wide
                                    text-gray-700
                                    ">
                            Assigned
                        </th>

                        <th class="
                                    border-b
                                    bg-gray-100
                                    px-2
                                    py-2
                                    font-semibold
                                    uppercase
                                    tracking-wide
                                    text-gray-7003
                                    ">
                            Supplier
                        </th>

                        <th class="
                                    border-b
                                    bg-gray-100
                                    px-2
                                    py-2
                                    font-semibold
                                    uppercase
                                    tracking-wide
                                    text-gray-700
                                    ">
                            Unit Price
                        </th>

                        <th
                            class="
                                border-b
                                bg-gray-100
                                px-2
                                py-2
                                text-center
                                font-semibold
                                uppercase
                                tracking-wide
                                text-gray-700
                            "
                        >
                            Disc %
                        </th>

                        <th
                            class="
                                border-b
                                bg-gray-100
                                px-2
                                py-2
                                text-center
                                font-semibold
                                uppercase
                                tracking-wide
                                text-gray-700
                            "
                        >
                            Disc Amt
                        </th>

                        <th
                            class="
                                border-b
                                bg-gray-100
                                px-2
                                py-2
                                text-center
                                font-semibold
                                uppercase
                                tracking-wide
                                text-gray-700
                            "
                        >
                            Tax
                        </th>

                        <th
                            class="
                                border-b
                                bg-gray-100
                                px-2
                                py-2
                                text-center
                                font-semibold
                                uppercase
                                tracking-wide
                                text-gray-700
                            "
                        >
                            Tax %
                        </th>

                        <th
                            class="
                                border-b
                                bg-gray-100
                                px-2
                                py-2
                                text-center
                                font-semibold
                                uppercase
                                tracking-wide
                                text-gray-700
                            "
                        >
                            Tax Amt
                        </th>

                        <th
                            class="
                                border-b
                                bg-gray-100
                                px-2
                                py-2
                                text-center
                                font-semibold
                                uppercase
                                tracking-wide
                                text-gray-700
                            "
                        >
                            Grand Total
                        </th>


                        @if ($this->canEdit)

                        <th
                            class="
                                border-b
                                bg-gray-100
                                px-2
                                py-2
                                font-semibold
                                uppercase
                                tracking-wide
                                text-gray-700
                                text-center
                            "
                        >
                            Action
                        </th>

                        @endif

                    </tr>

                    </thead>

                    <tbody>

                        @forelse ($items as $item)

                            @php
                                $statusColor = match ($item->status) {
                                    'draft' => 'gray',
                                    'assigned' => 'warning',
                                    'approved' => 'success',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'gray',
                                };
                            @endphp

                            <tr
                                wire:key="assignment-item-{{ $item->id }}"
                                class="transition-colors hover:bg-gray-50"
                            >

                                <td class="border px-2 py-1 text-center">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="border px-2 py-1 text-center">
                                    {{ $item->purchaseRequisitionItem?->item?->item_code ?? '-' }}
                                </td>

                                <td class="border px-2 py-1.5">
                                    {{ $item->purchaseRequisitionItem?->item?->item_name ?? '-' }}
                                </td>

                                <td class="border px-2 py-1.5">
                                    {{ $item->purchaseRequisitionItem?->remarks ?? '-' }}
                                </td>                          

                                <td class="border px-2 py-1.5 text-center">
                                    {{ $item->purchaseRequisitionItem?->uom?->uom_name ?? '-' }}
                                </td>

                                <td class="border px-2 py-1.5 text-center">
                                    {{ number_format($item->purchaseRequisitionItem?->quantity ?? 0, 2) }}
                                </td>

                                <td class="border px-2 py-1.5 text-center">

                                    @if ($this->canEdit && $editingRow === $item->id)

                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            wire:model.live="assignedQty.{{ $item->id }}"
                                            class="
                                            w-20
                                            rounded-md
                                            border-gray-300
                                            text-center
                                            text-xs
                                            py-1
                                            "
                                        >

                                    @else

                                        {{ number_format($item->assigned_qty, 2) }}

                                    @endif

                                </td>

                                <td class="border px-2 py-1.5 truncate">

                                    @if ($this->canEdit && $editingRow === $item->id)

                                        <select
                                            wire:model="selectedSupplier.{{ $item->id }}"
                                            class="w-full rounded-md border-gray-300 text-xs"
                                        >

                                            <option value="">
                                                -- Select Supplier --
                                            </option>

                                            @foreach ($suppliers as $supplier)

                                                <option value="{{ $supplier->id }}">
                                                    {{ $supplier->supplier_name }}
                                                </option>

                                            @endforeach

                                        </select>

                                    @else

                                        {{ $item->supplier?->supplier_name ?? '-' }}

                                    @endif

                                </td>

                                <td class="border px-2 py-1.5 text-right">

                                    @if ($this->canEdit && $editingRow === $item->id)

                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            wire:model.live="unitPrices.{{ $item->id }}"
                                            class="
                                                w-28
                                                rounded-md
                                                border-gray-300
                                                text-right
                                                text-xs
                                            "
                                        >

                                    @else

                                        Rp. {{ number_format($item->unit_price, 2) }}

                                    @endif

                                </td>

                                <td class="border px-2 py-1.5 text-right">

                                    @if ($this->canEdit && $editingRow === $item->id)

                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            wire:model.live="discountPercents.{{ $item->id }}"
                                            class="
                                                w-20
                                                rounded-md
                                                border-gray-300
                                                text-right
                                                text-xs
                                            "
                                        >

                                    @else

                                        {{ number_format((float) $item->discount_percent, 2) }} %

                                    @endif

                                </td>

                                <td class="border px-2 py-1.5 text-right">

                                    @if ($this->canEdit && $editingRow === $item->id)

                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            wire:model.live="discountAmounts.{{ $item->id }}"
                                            class="
                                                w-28
                                                rounded-md
                                                border-gray-300
                                                text-right
                                                text-xs
                                            "
                                        >

                                    @else

                                        Rp {{ number_format((float) $item->discount_amount, 2) }}

                                    @endif

                                </td>

                                <td class="border px-2 py-1.5">

                                    @if ($this->canEdit && $editingRow === $item->id)

                                        <select
                                            wire:model.live="selectedTax.{{ $item->id }}"
                                            class="w-full rounded-md border-gray-300 text-xs"
                                        >
                                            <option value="">-- Select Tax --</option>

                                            @foreach ($taxes as $tax)

                                                <option value="{{ $tax->id }}">
                                                    {{ $tax->tax_name }}
                                                </option>

                                            @endforeach

                                        </select>

                                    @else

                                        {{ $item->tax_name ?? '-' }}

                                    @endif

                                </td>

                                <td class="border px-2 py-1.5 text-right">

                                    {{ number_format((float) $item->tax_percent, 2) }} %

                                </td>

                                <td class="border px-2 py-1.5 text-right">

                                    Rp {{ number_format((float) $item->tax_amount, 2) }}

                                </td>

                                <td class="border px-2 py-1.5 text-right font-semibold text-blue-700">

                                    Rp {{ number_format((float) $item->grand_total, 2) }}

                                </td>


                                @if ($this->canEdit)

                                <td class="border px-2 py-1.5 text-center">

                                    @if ($this->canEdit && $editingRow === $item->id)

                                        <div class="flex justify-center gap-2">

                                            <x-filament::button
                                                color="success"
                                                size="xs"
                                                icon="heroicon-o-check-circle"
                                                wire:click="updateRow({{ $item->id }})"
                                                class="min-w-[92px] justify-center rounded-lg"
                                            >
                                                Update
                                            </x-filament::button>

                                            <x-filament::button
                                                color="gray"
                                                size="xs"
                                                icon="heroicon-o-x-mark"
                                                wire:click="cancelEdit"
                                                class="min-w-[92px] justify-center rounded-lg"
                                            >
                                                Cancel
                                            </x-filament::button>

                                        </div>

                                    @else

                                    <button
                                        type="button"
                                        wire:click="editRow({{ $item->id }})"
                                        class="
                                            inline-flex
                                            items-center
                                            justify-center
                                            gap-1
                                            rounded
                                            px-2
                                            py-1
                                            text-xs
                                            font-medium
                                            transition-all
                                            duration-150
                                            hover:bg-blue-50
                                        "
                                        style="color:#2563eb;"
                                    >
                                        <x-heroicon-o-pencil-square
                                            class="inline-block w-4 h-4 flex-shrink-0"
                                            style="display:inline-block;color:#2563eb;"
                                        />

                                        <span>Edit</span>
                                    </button>

                                    @endif

                                </td>

                                @endif


                            </tr>

                            @empty

                            <tr>

                                <td
                                    colspan="{{ $this->canEdit ? 16 : 15 }}"
                                    class="
                                        border
                                        py-20
                                        align-middle
                                    "
                                >

                                    <div class="text-center">

                                        {{-- Icon --}}

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

                                        {{-- Title --}}

                                        <div
                                            class="
                                                mt-5
                                                text-lg
                                                font-semibold
                                                text-gray-900
                                            "
                                        >
                                            No Assignment Items
                                        </div>

                                        {{-- Description --}}

                                        <div
                                            class="
                                                mt-2
                                                text-xs
                                                text-gray-500
                                            "
                                        >
                                            No material has been assigned to this document yet.
                                        </div>

                                    </div>

                                </td>

                            </tr>

                            @endempty

                 </tbody>

                <tfoot>

                    <tr>

                        <td
                            colspan="{{ $this->canEdit ? 11 : 10 }}"
                            class="h-14 border-0 bg-transparent"
                        >
                        </td>

                    </tr>

                </tfoot>
                
                </colgroup>

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
                            py-3
                            text-xs
                        "
                    >

                        {{-- Left Summary --}}

                        <div class="flex items-center gap-8">

                            <div>

                                <span class="text-gray-500">
                                    Total Lines :
                                </span>

                                <span style="color:#1d4ed8;font-weight:bold;">
                                    {{ $this->totalItems }}
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
                                mr-12
                            "
                        >

                            <div>

                                <span class="text-gray-500">
                                    Requested Qty :
                                </span>

                                <span style="color:#1d4ed8;font-weight:bold;">
                                    {{ number_format($this->totalRequestedQty, 2) }}
                                </span>

                            </div>

                            <div>

                                <span class="text-gray-500">
                                    Assigned Qty :
                                </span>

                                <span style="color:#1d4ed8;font-weight:bold;">
                                    {{ number_format($this->totalAssignedQty, 2) }}
                                </span>

                            </div>

                            <div>

                                <span class="text-gray-500">
                                    Total Amount :
                                </span>

                                <span style="color:#1d4ed8;font-weight:bold;">
                                    Rp {{ number_format($this->totalAmount, 2) }}
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- Close ERP Grid Container --}}
                </div>

                

</x-filament::section>
</div>