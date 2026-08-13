{{-- ===========================================================
 | ITEMS TABLE
 | Sprint MR-Print 2.3
 =========================================================== --}}

<div class="px-8 py-6">

    <h3 class="mb-4 border-b border-gray-200 pb-2 text-sm font-bold uppercase tracking-wider text-blue-700">
        Material Requisition Items
    </h3>

    <table class="w-full border border-gray-300 text-sm">

        <thead class="bg-gray-100">

            <tr>

                <th class="border border-gray-300 px-2 py-2 text-center w-12">
                    No
                </th>

                <th class="border border-gray-300 px-3 py-2 text-left w-36">
                    Item Code
                </th>

                <th class="border border-gray-300 px-3 py-2 text-left">
                    Description
                </th>

                <th class="border border-gray-300 px-3 py-2 text-left w-52">
                    Specification
                </th>

                <th class="border border-gray-300 px-2 py-2 text-center w-20">
                    UOM
                </th>

                <th class="border border-gray-300 px-2 py-2 text-right w-24">
                    Qty
                </th>

                <th class="border border-gray-300 px-3 py-2 text-center w-36">
                    Required Date
                </th>

                <th class="border border-gray-300 px-3 py-2 text-left w-48">
                    Remarks
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($this->record->items as $index => $item)

                <tr>

                    <td class="border border-gray-300 px-2 py-2 text-center">
                        {{ $index + 1 }}
                    </td>

                    <td class="border border-gray-300 px-3 py-2">
                        {{ $item->item_code }}
                    </td>

                    <td class="border border-gray-300 px-3 py-2">
                        {{ $item->item_name }}
                    </td>

                    <td class="border border-gray-300 px-3 py-2">
                        {{ $item->specification ?? '-' }}
                    </td>

                    <td class="border border-gray-300 px-2 py-2 text-center">
                        {{ $item->uom_code ?? '-' }}
                    </td>

                    <td class="border border-gray-300 px-2 py-2 text-right">
                        {{ number_format($item->requested_qty, 2) }}
                    </td>

                    <td class="border border-gray-300 px-3 py-2 text-center">
                        {{ optional($this->record->required_date)->format('d M Y') ?? '-' }}
                    </td>

                    <td class="border border-gray-300 px-3 py-2">
                        {{ $item->remarks ?? '-' }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="8" class="border border-gray-300 py-8 text-center text-gray-500">

                        No material requisition items found.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>