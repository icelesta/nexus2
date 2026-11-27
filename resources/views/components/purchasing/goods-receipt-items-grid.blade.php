<div
    class="space-y-6"
    x-on:goods-receipt-item-error.window="
        alert($event.detail.message)
    "
>

    {{-- ================================================================
        AVAILABLE SOURCE ITEMS
    ================================================================= --}}

    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
        {{-- Available Source Items Header --}}
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-blue-300 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-700/80 text-white">
                        <x-heroicon-o-clipboard-document-list class="h-5 w-5" />
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-white">
                            Available Source Items
                        </div>

                        <div class="text-xs text-blue-50">
                            Select items to receive
                        </div>
                    </div>
                </div>

                <div class="rounded-full border border-black/30 bg-black/10 px-3 py-1 text-xs font-medium text-black">
                    {{ count($sourceItems) }} Available
                </div>
            </div>
        </div>

        {{-- TABLE --}}

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-slate-50/90">

                    <tr class="border-b border-slate-200">

                        {{-- SELECT ALL --}}

                        <th class="w-12 px-4 py-2.5 text-center">

                            @if (! $viewOnly)

                                <input
                                    type="checkbox"
                                    wire:click="toggleSelectAll"
                                    @checked($this->allSourceItemsSelected())
                                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                >

                            @else

                                <span class="inline-flex h-4 w-4 items-center justify-center">
                                    <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                                </span>

                            @endif

                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Source No
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Source Date
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Item Code
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Item Name
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            UOM
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Source Qty
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Already Received
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Remaining
                        </th>

                        <th class="whitespace-nowrap px-4 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($sourceItems as $sourceItem)

                        @php

                            $sourceKey =
                                $this->sourceKeyFromSourceItem(
                                    $sourceItem
                                );

                            $isSelected =
                                isset(
                                    $selectedItems[$sourceKey]
                                );

                            $sourceQty =
                                $this->getSourceQty(
                                    $sourceItem
                                );

                            $alreadyReceived =
                                $this->getAlreadyReceived(
                                    $sourceItem
                                );

                            $remainingQty =
                                $this->getRemainingQty(
                                    $sourceItem
                                );

                            $isComplete =
                                $remainingQty <= 0;

                        @endphp

                        <tr
                            class="
                                transition-colors
                                {{ $isSelected
                                    ? 'bg-blue-50/60'
                                    : 'bg-white hover:bg-slate-50'
                                }}
                            "
                        >

                            {{-- SELECT --}}

                            <td class="px-4 py-3 text-center">

                                <input
                                    type="checkbox"
                                    wire:click="selectItem({{ $sourceItem->id }})"
                                    @checked($isSelected)
                                    @disabled($isComplete || $itemsReadOnly || $viewOnly)
                                    @disabled($itemsReadOnly || $viewOnly)
                                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-40"
                                >

                            </td>

                            {{-- SOURCE NO --}}

                            <td class="whitespace-nowrap px-3 py-3">

                                <span class="font-semibold text-slate-700">
                                    {{ $this->getSourceNumber() }}
                                </span>

                            </td>

                            {{-- SOURCE DATE --}}

                            <td class="whitespace-nowrap px-3 py-3 text-slate-500">

                                {{ $this->getSourceDate() ?? '-' }}

                            </td>

                            {{-- ITEM CODE --}}

                            <td class="whitespace-nowrap px-3 py-3">

                                <span class="font-semibold text-slate-700">
                                    {{ $this->getSourceCode($sourceItem) }}
                                </span>

                            </td>

                            {{-- ITEM NAME --}}

                            <td class="px-3 py-3">

                                <div class="font-medium text-slate-800">
                                    {{ $this->getSourceName($sourceItem) }}
                                </div>

                            </td>

                            {{-- UOM --}}

                            <td class="px-3 py-3 text-center">

                                <span class="inline-flex rounded-md bg-slate-100 px-2 py-1 text-[11px] font-semibold text-slate-600">
                                    {{ $this->getSourceUom($sourceItem) }}
                                </span>

                            </td>

                            {{-- SOURCE QTY --}}

                            <td class="px-3 py-3 text-right font-semibold text-slate-700">

                                {{ rtrim(rtrim(number_format($sourceQty, 2, '.', ','), '0'), '.') }}

                            </td>

                            {{-- ALREADY RECEIVED --}}

                            <td class="px-3 py-3 text-right text-slate-500">

                                {{ rtrim(rtrim(number_format($alreadyReceived, 2, '.', ','), '0'), '.') }}

                            </td>

                            {{-- REMAINING --}}

                            <td class="px-3 py-3 text-right">
                                <span class="inline-flex min-w-[32px] justify-center rounded-md border px-2 py-1 text-xs font-semibold
                                    {{ $remainingQty < 0
                                        ? 'border-red-200 bg-red-50 text-red-700'
                                        : 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                    }}">
                                    {{ rtrim(rtrim(number_format($remainingQty, 2, '.', ','), '0'), '.') }}
                                </span>
                            </td>

                            {{-- STATUS --}}

                            <td class="px-4 py-3 text-center">

                                @php
                                    $itemStatus = $this->getItemStatus($sourceItem);
                                @endphp

                                <span class="{{ $this->getSourceStatusClass($itemStatus) }}">
                                    {{ $itemStatus }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="10"
                                class="px-6 py-10 text-center"
                            >

                                <div class="flex flex-col items-center justify-center">

                                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <x-heroicon-o-inbox class="h-5 w-5" />
                                    </div>

                                    <div class="text-sm font-semibold text-slate-700">
                                        No available source items
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        There are no outstanding items available for receiving.
                                    </div>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ================================================================
        RECEIVING ITEMS
    ================================================================= --}}
    <div class="overflow-hidden rounded-xl border border-slate-800 bg-white shadow-sm">

        {{-- HEADER --}}

        <div class="flex items-center justify-between border-b border-blue-200 bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 px-4 py-3">

            <div class="flex items-center gap-3">

                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/15 text-white ring-1 ring-white/20">
                    <x-heroicon-o-arrow-down-tray class="h-5 w-5" />
                </div>

                <div>
                    <div class="text-sm font-semibold text-white">
                        Receiving Items
                    </div>

                    <div class="text-[11px] text-blue-100">
                        Enter the actual received quantity
                    </div>
                </div>

            </div>

            <div class="rounded-full border border-black/20 bg-black/10 px-3 py-1 text-[11px] font-semibold text-black">
                {{ $this->getDisplayReceivingItems()->count() }} Items
            </div>

        </div>

        {{-- TABLE --}}

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-blue-50/70">

                    <tr class="border-b border-blue-100">

                        <th class="w-12 px-3 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            No.
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Item
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Description
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            UOM
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Source Qty
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Already Received
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Remaining
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-blue-700">
                            Receive Qty
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Unit Price
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Amount
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Warehouse
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Allocation Journal
                        </th>

                        <th class="whitespace-nowrap px-3 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            QC Check
                        </th>

                        <th class="whitespace-nowrap px-4 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($this->getDisplayReceivingItems() as $index => $item)

                        @php

                            $sourceItem =
                                $item->purchaseOrderItem
                                ??
                                $item->assignmentDirectMarketItem;

                            $sourceQty =
                                $sourceItem
                                    ? $this->getSourceQty(
                                        $sourceItem
                                    )
                                    : 0;

                            $alreadyReceivedBase =
                                $sourceItem
                                    ? $this->getAlreadyReceived(
                                        $sourceItem
                                    )
                                    : 0;

                            $currentReceiveQty =
                                max(
                                    0,
                                    (float) (
                                        $this->receiveQty[$item->getKey()]
                                        ?? 0
                                    )
                                );

                            $alreadyReceived = $alreadyReceivedBase;

                            $remainingBeforeInput = max(
                                0,
                                $sourceQty - $alreadyReceivedBase
                            );

                            $remainingQty = ($itemsReadOnly || $viewOnly)
                                ? max(0, $remainingBeforeInput)
                                : $remainingBeforeInput - $currentReceiveQty;
                        @endphp

                        <tr class="bg-white transition-colors hover:bg-blue-50/30">

                            {{-- NO --}}

                            <td class="px-3 py-3 text-center text-xs font-semibold text-slate-500">
                                {{ $index + 1 }}
                            </td>

                            {{-- ITEM --}}

                            <td class="whitespace-nowrap px-3 py-3">

                                <div class="font-semibold text-slate-700">
                                    {{ $sourceItem?->item_code ?? '-' }}
                                </div>

                            </td>

                            {{-- DESCRIPTION --}}

                            <td class="px-3 py-3">

                                <div class="max-w-[260px] truncate font-medium text-slate-800">
                                    {{ $sourceItem?->item_name ?? '-' }}
                                </div>

                            </td>

                            {{-- UOM --}}

                            <td class="px-3 py-3 text-center">

                                <span class="inline-flex rounded-md bg-slate-100 px-2 py-1 text-[11px] font-semibold text-slate-600">
                                    {{
                                        $sourceItem?->uom_code
                                        ??
                                        $sourceItem?->uom_name
                                        ??
                                        '-'
                                    }}
                                </span>

                            </td>

                            {{-- SOURCE QTY --}}

                            <td class="px-3 py-3 text-right font-semibold text-slate-700">

                                {{ rtrim(rtrim(number_format($sourceQty, 2, '.', ','), '0'), '.') }}

                            </td>

                            {{-- ALREADY RECEIVED --}}

                            <td class="px-3 py-3 text-right text-slate-500">

                                {{ rtrim(rtrim(number_format($alreadyReceived, 2, '.', ','), '0'), '.') }}

                            </td>

                            {{-- REMAINING --}}

                            <td class="px-3 py-3 text-right">

                                <span class="inline-flex min-w-[32px] justify-center rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">

                                    {{ rtrim(rtrim(number_format($remainingQty, 2, '.', ','), '0'), '.') }}

                                </span>

                            </td>

                            {{-- RECEIVE QTY --}}

                            <td class="px-3 py-3 text-center">

                            @if ($itemsReadOnly || $viewOnly)

                                <span class="inline-flex min-w-[80px] justify-center rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-semibold text-slate-700">
                                    {{ rtrim(rtrim(number_format(
                                        $this->getDisplayReceiveQty($item),
                                        4,
                                        '.',
                                        ','
                                    ), '0'), '.') }}
                                </span>

                            @else

                                <input
                                    type="number"
                                    min="0"
                                    max="{{ $remainingBeforeInput }}"
                                    step="0.0001"
                                    inputmode="decimal"
                                    wire:model.live.debounce.150ms="receiveQty.{{ $item->getKey() }}"
                                    class="w-24 rounded-lg border-blue-200 bg-blue-50/50 px-2 py-1.5 text-center text-sm font-semibold text-blue-700 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    style="-moz-appearance: textfield;"
                                >

                            @endif

                            </td>

                            {{-- =========================================================
                                 UNIT PRICE
                            ========================================================= --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap">

                                <span class="font-medium text-gray-700">
                                    Rp
                                    {{ number_format(
                                        $this->getUnitPrice($sourceItem),
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </span>

                            </td>


                            {{-- =========================================================
                                 AMOUNT
                            ========================================================= --}}
                            <td class="px-4 py-3 text-right whitespace-nowrap">

                                <span class="font-semibold text-gray-800">

                                    Rp
                                    {{ number_format(
                                        $this->getAmount($sourceItem),
                                        2,
                                        ',',
                                        '.'
                                    ) }}

                                </span>

                            </td>

                            {{-- WAREHOUSE --}}

                            <td class="px-3 py-3 text-center">

                                <span class="inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-[10px] font-medium text-slate-500">

                                    {{ $item->warehouse?->warehouse_name ?? '-' }}

                                </span>

                            </td>

                            {{-- =========================================================
                                 ALLOCATION JOURNAL
                            ========================================================= --}}
                            <td class="px-4 py-3 whitespace-nowrap">

                                @php
                                    $allocationJournal =
                                        $this->getAllocationJournal(
                                            $sourceItem
                                        );
                                @endphp

                                @if($allocationJournal !== '-')

                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            rounded-md
                                            bg-blue-50
                                            px-2.5
                                            py-1
                                            text-xs
                                            font-semibold
                                            text-blue-700
                                            ring-1
                                            ring-inset
                                            ring-blue-200
                                        "
                                    >
                                        {{ $allocationJournal }}
                                    </span>

                                @else

                                    <span class="text-xs text-gray-400">
                                        -
                                    </span>

                                @endif

                            </td>

                            {{-- QC CHECK --}}

                            <td class="px-3 py-3 text-center">

                                @if ($item->exists)

                                    <div class="flex flex-col items-center gap-1">

                                        @if ($this->canManageQc() && ! $itemsReadOnly && ! $viewOnly)

                                            <label class="inline-flex cursor-pointer items-center gap-2">

                                                <input
                                                    type="checkbox"
                                                    @checked((bool) $item->qc_passed)
                                                    wire:click="toggleQc({{ $item->getKey() }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="toggleQc({{ $item->getKey() }})"
                                                    class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                                >

                                                <span class="text-xs font-semibold
                                                    {{ $item->qc_passed
                                                        ? 'text-emerald-700'
                                                        : 'text-amber-600'
                                                    }}">
                                                    {{ $item->qc_passed ? 'Passed' : 'Unpassed' }}
                                                </span>

                                            </label>

                                        @else

                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                                {{ $item->qc_passed
                                                    ? 'bg-emerald-100 text-emerald-700'
                                                    : 'bg-amber-100 text-amber-700'
                                                }}">
                                                {{ $item->qc_passed ? 'Passed' : 'Unpassed' }}
                                            </span>

                                        @endif

                                        @if ($item->qc_checked_at)
                                            <span class="text-[10px] text-slate-400">
                                                {{ $item->qc_checked_at->format('d M Y H:i') }}
                                            </span>
                                        @endif

                                    </div>

                                @else

                                    <span class="text-[10px] font-medium text-slate-400">
                                        Save Draft First
                                    </span>

                                @endif

                            </td>

                            {{-- ACTION --}}

                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-300"
                                    title="Saved items cannot be removed"
                                >
                                    <x-heroicon-o-lock-closed class="h-4 w-4" />
                                </span>
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="14"
                                class="px-6 py-12 text-center"
                            >

                                <div class="flex flex-col items-center justify-center">

                                    <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-blue-50 text-blue-500">
                                        <x-heroicon-o-arrow-down-tray class="h-5 w-5" />
                                    </div>

                                    <div class="text-sm font-semibold text-slate-700">
                                        No receiving items selected
                                    </div>

                                    <div class="mt-1 max-w-md text-xs text-slate-500">
                                        Select an available source item above to add it to this Receiving Record.
                                    </div>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- FOOTER --}}

        @if (! $viewOnly)

            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-4 py-3">

                <div class="flex items-center gap-2 text-xs text-slate-500">

                    <x-heroicon-o-information-circle class="h-4 w-4 text-blue-500" />

                    <span>
                        Draft Receive Qty does not reduce source Remaining. Final quantity validation is performed when the RR is submitted.
                    </span>

                </div>

            </div>

        @endif

    </div>

</div>