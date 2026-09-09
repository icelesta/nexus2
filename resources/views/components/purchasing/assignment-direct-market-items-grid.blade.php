<div>
    <x-filament::section>

        <div class="flex items-start justify-between gap-6">

            <div>
                <div class="text-base font-semibold">
                    Assignment Items
                </div>

                <div class="text-sm text-gray-500">
                    Assign supplier, quantity and pricing for each Direct Market item.
                </div>
            </div>

            <div class="text-right">
                @if ($this->getAdmLimitAmount() !== null)

                    <div class="text-sm font-bold text-red-600">
                        LIMIT AMOUNT ADM
                        Rp. {{ number_format($this->getAdmLimitAmount(), 2, ',', '.') }}
                    </div>

                @else

                    <div class="text-sm font-bold text-red-600">
                        LIMIT AMOUNT ADM NOT CONFIGURED
                    </div>

                @endif

                <div class="text-sm font-semibold text-gray-500">
                    Maximum Assignment Direct Market amount.
                </div>
            </div>

        </div>


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

                        <tr
                            class="
                                border-b
                                text-[11px]
                                font-semibold
                                uppercase
                                tracking-wide
                                text-gray-700
                            "
                        >

                            <th class="border-b bg-gray-100 px-2 py-2">
                                #
                            </th>

                            <th class="border-b bg-gray-100 px-2 py-2">
                                Item Code
                            </th>

                            <th class="border-b bg-gray-100 px-2 py-2">
                                Item Name
                            </th>

                            <th class="border-b bg-gray-100 px-2 py-2">
                                Description
                            </th>

                            <th class="border-b bg-gray-100 px-2 py-2">
                                UOM
                            </th>

                            <th class="border-b bg-gray-100 px-2 py-2 text-center">
                                Requested Qty
                            </th>

                            <th class="border-b bg-gray-100 px-2 py-2 text-center">
                                Assigned Qty
                            </th>

                            <th class="border-b bg-gray-100 px-2 py-2">
                                Supplier
                            </th>

                            <th class="border-b bg-gray-100 px-2 py-2 text-right">
                                Unit Price
                            </th>

                            <th class="border-b bg-gray-100 px-2 py-2 text-right">
                                Disc %
                            </th>

                            <th class="border-b bg-gray-100 px-2 py-2 text-right">
                                Disc Amt
                            </th>

                            <th class="border-b bg-gray-100 px-2 py-2 text-right">
                                Amount
                            </th>

                            @if ($this->canEdit)

                            <th class="border-b bg-gray-100 px-2 py-2 text-center">
                                    Action
                            </th>

                            @endif

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($items as $item)

                            <tr
                                wire:key="assignment-direct-market-item-{{ $item->id }}"
                                class="transition-colors hover:bg-gray-50"
                            >

                                <td class="border px-2 py-1 text-center">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="border px-2 py-1.5">
                                    {{ $item->item_code ?? $item->item?->item_code ?? '-' }}
                                </td>

                                <td class="border px-2 py-1.5">
                                    {{ $item->item_name ?? $item->item?->item_name ?? '-' }}
                                </td>

                                <td class="border px-2 py-1.5">
                                    {{ $item->item_description ?? '-' }}
                                </td>

                                <td class="border px-2 py-1.5 text-center">
                                    {{ $item->uom_name ?? $item->uom?->uom_name ?? '-' }}
                                </td>

                                <td class="border px-2 py-1.5 text-center">
                                    {{ number_format((float) $item->requested_qty, 2) }}
                                </td>

                                <td class="border px-2 py-1.5 text-center">

                                    @if (
                                        $this->canEdit
                                        && $editingRow === $item->id
                                    )

                                        <input
                                            type="number"
                                            step="0.0001"
                                            min="0"
                                            max="{{ $item->requested_qty }}"
                                            wire:model="assignedQty.{{ $item->id }}"
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

                                        {{ number_format((float) $item->assigned_qty, 2) }}

                                    @endif

                                </td>

                                <td class="border px-2 py-1.5 truncate">

                                    @if (
                                        $this->canEdit
                                        && $editingRow === $item->id
                                    )

                                        <select
                                            wire:model="selectedSupplier.{{ $item->id }}"
                                            class="
                                                w-full
                                                rounded-md
                                                border-gray-300
                                                text-xs
                                            "
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

                                    @if (
                                        $this->canEdit
                                        && $editingRow === $item->id
                                    )

                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            wire:model="unitPrices.{{ $item->id }}"
                                            class="
                                                w-28
                                                rounded-md
                                                border-gray-300
                                                text-right
                                                text-xs
                                            "
                                        >

                                    @else

                                        Rp {{ number_format((float) $item->unit_price, 2) }}

                                    @endif

                                </td>
                                
                                <td class="border px-2 py-1.5 text-right">

                                    @if (
                                        $this->canEdit
                                        && $editingRow === $item->id
                                    )

                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="100"
                                            wire:model="discountPercents.{{ $item->id }}"
                                            class="
                                                w-20
                                                rounded-md
                                                border-gray-300
                                                text-right
                                                text-xs
                                            "
                                        >

                                    @else

                                        {{ number_format((float) $item->discount_percent, 2) }}%

                                    @endif

                                </td>

                                <td class="border px-2 py-1.5 text-right">

                                    @if (
                                        $this->canEdit
                                        && $editingRow === $item->id
                                    )

                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            wire:model="discountAmounts.{{ $item->id }}"
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


                                <td class="border px-2 py-1.5 text-right font-semibold">
                                    @php
                                        $rowAssignedQty = (float) (
                                            $this->assignedQty[$item->id]
                                            ?? $item->assigned_qty
                                            ?? 0
                                        );

                                        $rowUnitPrice = (float) (
                                            $this->unitPrices[$item->id]
                                            ?? $item->unit_price
                                            ?? 0
                                        );

                                        $rowDiscountAmount = (float) (
                                            $this->discountAmounts[$item->id]
                                            ?? $item->discount_amount
                                            ?? 0
                                        );

                                        $rowAmount = max(
                                            0,
                                            ($rowAssignedQty * $rowUnitPrice) - $rowDiscountAmount
                                        );
                                    @endphp

                                    Rp {{ number_format($rowAmount, 2) }}
                                </td>

                                @if ($this->canEdit)

                                    <td class="border px-2 py-1.5 text-center">

                                        @if ($editingRow === $item->id)

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
                                                color="primary"
                                                size="xs"
                                                icon="heroicon-o-check-badge"
                                                wire:click="saveAndClose({{ $item->id }})"
                                                class="min-w-[110px] justify-center rounded-lg"
                                            >
                                                Save & Close
                                            </x-filament::button>

                                            <button
                                                type="button"
                                                wire:click="resetPricing({{ $item->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="resetPricing({{ $item->id }})"
                                                class="min-w-[92px] justify-center rounded-lg"
                                            >
                                                <x-heroicon-o-arrow-path class="h-4 w-4" />
                                                Reset
                                            </button>

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

                                                <span>
                                                    Edit
                                                </span>

                                            </button>

                                        @endif

                                    </td>
                                    



                                @endif

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="{{ $this->canEdit ? 12 : 11 }}"
                                    class="border py-20 align-middle"
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

                                            <x-heroicon-o-clipboard-document-list
                                                class="h-10 w-10 text-gray-400"
                                            />

                                        </div>

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

                                        <div
                                            class="
                                                mt-2
                                                text-xs
                                                text-gray-500
                                            "
                                        >
                                            No Direct Market item has been assigned to this document yet.
                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

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

        </div>

    </x-filament::section>
</div>