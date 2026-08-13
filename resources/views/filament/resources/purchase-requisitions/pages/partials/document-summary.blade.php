{{-- ===========================================================
 | DOCUMENT SUMMARY
 | Sprint MR-Print 2.4
 =========================================================== --}}

@php
    $items = $this->record->items;

    $totalItems = $items->count();

    $totalQty = $items->sum('requested_qty');
@endphp

<div class="border-t border-gray-300 px-8 py-6 bg-gray-50">

    <h3 class="mb-4 border-b border-gray-200 pb-2 text-sm font-bold uppercase tracking-wider text-blue-700">
        Document Summary
    </h3>

    <div class="grid grid-cols-2 gap-10">

        <table class="w-full text-sm">

            <tbody>

                <tr>

                    <td class="w-56 py-2 font-medium text-gray-600">
                        Total Items
                    </td>

                    <td>
                        : {{ number_format($totalItems) }}
                    </td>

                </tr>

                <tr>

                    <td class="py-2 font-medium text-gray-600">
                        Total Requested Quantity
                    </td>

                    <td>
                        : {{ number_format($totalQty, 2) }}
                    </td>

                </tr>

            </tbody>

        </table>

        <table class="w-full text-sm">

            <tbody>

                <tr>

                    <td class="w-44 py-2 font-medium text-gray-600">
                        Created By
                    </td>

                    <td>
                        : {{ $this->record->createdBy?->name ?? '-' }}
                    </td>

                </tr>

                <tr>

                    <td class="py-2 font-medium text-gray-600">
                        Created At
                    </td>

                    <td>
                        : {{ optional($this->record->created_at)->format('d M Y H:i') }}
                    </td>

                </tr>

                <tr>

                    <td class="py-2 font-medium text-gray-600">
                        Last Updated
                    </td>

                    <td>
                        : {{ optional($this->record->updated_at)->format('d M Y H:i') }}
                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>