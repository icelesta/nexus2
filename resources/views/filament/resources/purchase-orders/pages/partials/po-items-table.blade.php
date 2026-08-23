{{-- ==========================================================
| Nexus ERP 2.0
| Purchase Order Items Table
| Corporate Edition V2
========================================================== --}}

<style>

    .po-items{

        width:100%;

        border-collapse:collapse;

        margin-top:8mm;

        font-size:10pt;

        color:#222;

    }

    .po-items thead{

        display:table-header-group;

    }

    .po-items tbody{

        display:table-row-group;

    }

    .po-items tfoot{

        display:table-footer-group;

    }

    .po-items tr{

        page-break-inside:avoid;

    }

    .po-items th{

        background:#12385d;

        color:#fff;

        border:1px solid #6d89a6;

        padding:9px 6px;

        text-align:center;

        font-size:10pt;

        font-weight:700;

        white-space:nowrap;

    }

    .po-items td{

        border:1px solid #c8d2dc;

        padding:7px;

        vertical-align:top;

        line-height:18px;

    }

    .center{

        text-align:center;

    }

    .right{

        text-align:right;

    }

    .description{

        width:48%;

    }

    .unit{

        width:70px;

    }

    .qty{

        width:80px;

    }

    .price{

        width:130px;

    }

    .amount{

        width:140px;

    }

    .item-code{

        color:#7b8794;

        font-size:9px;

        margin-bottom:3px;

    }

    .item-name{

        font-size:11px;

        font-weight:700;

        color:#163d63;

        text-transform:uppercase;

    }

    .item-spec{

        margin-top:5px;

        font-size:9px;

        color:#666;

        font-style:italic;

    }

    .money{

        font-weight:600;

    }

    .line-total{

        font-weight:700;

        color:#163d63;

    }

    .empty-row td{

        height:24px;

    }

    @media print{

        .po-items th{

            background:#12385d !important;

            color:#fff !important;

            print-color-adjust:exact;

            -webkit-print-color-adjust:exact;

        }

    }

</style>

<table class="po-items">

    <thead>

        <tr>

            <th style="width:40px">

                NO

            </th>

            <th class="description">

                DESCRIPTION

            </th>

            <th class="unit">

                UNIT

            </th>

            <th class="qty">

                QTY

            </th>

            <th class="price">

                UNIT PRICE

            </th>

            <th class="amount">

                AMOUNT

            </th>

        </tr>

    </thead>

<tbody>

@php
    $totalItems = $items->count();
@endphp

@forelse($items as $index => $item)

    @php

        $qty = (float) ($item->ordered_qty ?? 0);

        $price = (float) ($item->unit_price ?? 0);

        $discount = (float) ($item->discount_amount ?? 0);

        $gross = $qty * $price;

        $amount = $gross - $discount;

    @endphp

    <tr>

        {{-- ========================================= --}}
        {{-- NO --}}
        {{-- ========================================= --}}

        <td class="center">

            {{ $index + 1 }}

        </td>

        {{-- ========================================= --}}
        {{-- DESCRIPTION --}}
        {{-- ========================================= --}}

        <td>

            {{-- Item Code --}}

            <div class="item-code">

                {{ $item->item_code }}

            </div>

            {{-- Item Name --}}

            <div class="item-name">

                {{ strtoupper($item->item_name) }}

            </div>

            {{-- Description --}}

            @if(!empty($item->item_description))

                <div style="margin-top:4px;">

                    {{ $item->item_description }}

                </div>

            @endif

            {{-- Specification --}}

            @if(!empty($item->specification))

                <div class="item-spec">

                    <strong>Spec :</strong>

                    {{ $item->specification }}

                </div>

            @endif

        </td>

        {{-- ========================================= --}}
        {{-- UNIT --}}
        {{-- ========================================= --}}

        <td class="center">

            {{ $item->uom_code }}

        </td>

        {{-- ========================================= --}}
        {{-- QTY --}}
        {{-- ========================================= --}}

        <td class="right">

            {{ number_format($qty,2) }}

        </td>

        {{-- ========================================= --}}
        {{-- UNIT PRICE --}}
        {{-- ========================================= --}}

        <td class="right money">

            {{ number_format($price,2) }}

        </td>

        {{-- ========================================= --}}
        {{-- AMOUNT --}}
        {{-- ========================================= --}}

        <td class="right line-total">

            {{ number_format($amount,2) }}

        </td>

    </tr>

@empty

<tr>

    <td colspan="6"
        style="
            text-align:center;
            padding:25px;
            color:#777;
        ">

        No Purchase Order Item Found

    </td>

</tr>

@endforelse


{{-- ========================================= --}}
{{-- EMPTY ROW --}}
{{-- ========================================= --}}

@php

    /*
    |--------------------------------------------------------------------------
    | Corporate Layout Rule
    |--------------------------------------------------------------------------
    | Maksimal tampilkan 3 baris kosong setelah item terakhir.
    | Jangan memaksa jumlah baris minimum.
    */

    $emptyRows = max(0, 1 - $items->count());

@endphp

@for($i=0;$i<$emptyRows;$i++)

<tr class="empty-row">

    <td>&nbsp;</td>

    <td></td>

    <td></td>

    <td></td>

    <td></td>

    <td></td>

</tr>

@endfor

</tbody>

<tfoot>

    <tr>

        <td colspan="6"
            style="
                border:1px solid #c8d2dc;
                background:#f8fafc;
                padding:8px 12px;
            ">

            <table
                style="
                    width:100%;
                    border-collapse:collapse;
                    border:none;
                ">

                <tr>

                    {{-- LEFT --}}

                    <td
                        style="
                            border:none;
                            padding:0;
                            vertical-align:middle;
                            color:#555;
                            font-size:10px;
                        ">

                        <strong>Total Item :</strong>

                        {{ number_format($totalItems) }}

                    </td>

                </tr>

            </table>

        </td>

    </tr>

</tfoot>

</table>

