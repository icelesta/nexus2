<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        {{ $documentNumber }}
    </title>

    <style>

        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #111827;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .document {
            width: 100%;
            border: 1px solid #7d93aa;
            background: #ffffff;
            box-sizing: border-box;
            overflow: hidden;
        }

        .header-logo {
            width: 90px;
            height: 120px;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            border-right: 1px solid #7d93aa;
        }

        .header-logo img {
            width: 65px;
            height: 65px;
            object-fit: contain;
        }

        .company-cell {
            padding: 16px;
            vertical-align: middle;
        }

        .company-name {
            font-size: 18px;
            font-weight: 700;
            color: #12385d;
            line-height: 1.25;
        }

        .company-info {
            margin-top: 6px;
            font-size: 9px;
            color: #333333;
        }

        .document-info {
            width: 205px;
            padding: 0;
            vertical-align: top;
            border-left: 1px solid #7d93aa;
        }

        .document-title {
            padding: 10px 6px;
            background: #12385d;
            color: #ffffff;
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
        }

        .info-label {
            width: 70px;
            padding: 7px;
            font-size: 9px;
            font-weight: 600;
            border-bottom: 1px solid #d7dee7;
        }

        .info-value {
            padding: 7px;
            font-size: 9px;
            font-weight: 600;
            color: #12385d;
            border-bottom: 1px solid #d7dee7;
        }

        .section {
            padding: 14px 16px;
            border-top: 1px solid #7d93aa;
        }

        .section-title {
            margin-bottom: 10px;
            font-size: 10px;
            font-weight: 700;
            color: #12385d;
            text-transform: uppercase;
        }

        .information-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 9px;
        }

        .information-label {
            width: 125px;
        }

        .information-separator {
            width: 15px;
            text-align: center;
        }

        .items-table {
            width: 100%;
            border: 1px solid #cbd5e1;
        }

        .items-table th {
            padding: 6px 4px;
            background: #eef3f8;
            border: 1px solid #cbd5e1;
            color: #12385d;
            font-size: 7px;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
        }

        .items-table td {
            padding: 6px 4px;
            border: 1px solid #cbd5e1;
            font-size: 7px;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .signatures {
            padding: 20px 10px 8px;
            border-top: 1px solid #7d93aa;
        }

        .signature-title {
            font-size: 9px;
            font-weight: 700;
        }

        .signature-space {
            height: 55px;
            margin: 0 18px;
            border-bottom: 1px solid #9ca3af;
        }

        .signature-name {
            margin-top: 6px;
            font-size: 9px;
        }

    </style>

</head>

<body>

@php

    /*
    |--------------------------------------------------------------------------
    | BASIC DOCUMENT DATA
    |--------------------------------------------------------------------------
    */

    $companyName =
        $record->company?->company_name
        ?? $record->company?->name
        ?? 'PT BESMINDO MATERI SEWATAMA';

    $companyTagline =
        'Oilfield Equipment Sales & Rental, Drilling & Work Over Rig Services';

    $companyService =
        'Light Vehicle & Logistics Yard Duri';

    $requester =
        $record->requester?->name
        ?? $record->requester?->full_name
        ?? '-';

    $status =
        strtoupper(
            (string) ($record->status ?? '-')
        );

    $requestDate =
        $record->request_date
            ? \Carbon\Carbon::parse($record->request_date)->format('d M Y')
            : '-';

    $requiredDate =
        $record->required_date
            ? \Carbon\Carbon::parse($record->required_date)->format('d M Y')
            : '-';

    $logoPath =
        public_path('images/company/icon.png');

    $logoSrc = null;

    if (is_file($logoPath)) {

        $logoSrc =
            'data:image/png;base64,' .
            base64_encode(
                file_get_contents($logoPath)
            );

    }

@endphp


<div class="document">

    {{-- ========================================================= --}}
    {{-- DOCUMENT HEADER --}}
    {{-- ========================================================= --}}

    <table>

        <tr>

            {{-- LOGO --}}
            <td class="header-logo">

                @if($logoSrc)

                    <img
                        src="{{ $logoSrc }}"
                        alt="Besmindo Logo"
                    >

                @endif

            </td>


            {{-- COMPANY --}}
            <td class="company-cell">

                <div
                    style="
                        font-size:16px;
                        font-weight:700;
                        color:#123f6b;
                    "
                >
                    PT BESMINDO MATERI SEWATAMA
                </div>

                <div class="company-info">
                    {{ $companyTagline }}
                </div>

                <div class="company-info">
                    {{ $companyService }}
                </div>

            </td>


            {{-- DOCUMENT INFORMATION --}}
            <td class="document-info">

                <div class="document-title">
                    MATERIAL<br>
                    REQUISITION
                </div>


                <table>

                    <tr>

                        <td class="info-label">
                            Number
                        </td>

                        <td class="info-value">
                            {{ $documentNumber }}
                        </td>

                    </tr>


                    <tr>

                        <td class="info-label">
                            Status
                        </td>

                        <td class="info-value">
                            {{ $status }}
                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>


    {{-- =========================================================
         REQUEST / ORGANIZATION INFORMATION
    ========================================================= --}}

    <div
        class="section"
        style="
            width:100%;
            box-sizing:border-box;
            padding:18px 20px 20px;
            margin:0;
        "
    >

        <table
            style="
                width:100%;
                box-sizing:border-box;
                border-collapse:collapse;
                table-layout:fixed;
            "
        >

            <tr>

                {{-- =====================================================
                     REQUEST INFORMATION
                ====================================================== --}}

                <td
                    style="
                        width:50%;
                        vertical-align:top;
                        padding:0 10px 0 0;
                        box-sizing:border-box;
                    "
                >

                    <div
                        class="section-title"
                        style="
                            margin:0 0 12px 0;
                            padding:0;
                            font-size:10px;
                            line-height:1.2;
                            font-weight:700;
                            color:#0057ff;
                            text-transform:uppercase;
                        "
                    >
                        REQUEST INFORMATION
                    </div>


                    <table
                        class="information-table"
                        style="
                            width:100%;
                            box-sizing:border-box;
                            border-collapse:collapse;
                            table-layout:fixed;
                            font-size:9px;
                        "
                    >

                        <tr>

                            <td
                                class="information-label"
                                style="
                                    width:31%;
                                    padding:0 0 7px 0;
                                    vertical-align:top;
                                    white-space:nowrap;
                                "
                            >
                                Request Date
                            </td>

                            <td
                                class="information-separator"
                                style="
                                    width:4%;
                                    padding:0 0 7px 0;
                                    vertical-align:top;
                                    text-align:left;
                                "
                            >
                                :
                            </td>

                            <td
                                style="
                                    width:65%;
                                    padding:0 0 7px 2px;
                                    vertical-align:top;
                                    white-space:normal;
                                    overflow-wrap:break-word;
                                    word-wrap:break-word;
                                "
                            >
                                {{ $requestDate }}
                            </td>

                        </tr>


                        <tr>

                            <td
                                class="information-label"
                                style="
                                    width:31%;
                                    padding:0 0 7px 0;
                                    vertical-align:top;
                                    white-space:nowrap;
                                "
                            >
                                Required Date
                            </td>

                            <td
                                class="information-separator"
                                style="
                                    width:4%;
                                    padding:0 0 7px 0;
                                    vertical-align:top;
                                    text-align:left;
                                "
                            >
                                :
                            </td>

                            <td
                                style="
                                    width:65%;
                                    padding:0 0 7px 2px;
                                    vertical-align:top;
                                    white-space:normal;
                                    overflow-wrap:break-word;
                                    word-wrap:break-word;
                                "
                            >
                                {{ $requiredDate }}
                            </td>

                        </tr>


                        <tr>

                            <td
                                class="information-label"
                                style="
                                    width:31%;
                                    padding:0 0 7px 0;
                                    vertical-align:top;
                                    white-space:nowrap;
                                "
                            >
                                Remarks
                            </td>

                            <td
                                class="information-separator"
                                style="
                                    width:4%;
                                    padding:0 0 7px 0;
                                    vertical-align:top;
                                    text-align:left;
                                "
                            >
                                :
                            </td>

                            <td
                                style="
                                    width:65%;
                                    padding:0 0 7px 2px;
                                    vertical-align:top;
                                    white-space:normal;
                                    overflow-wrap:break-word;
                                    word-wrap:break-word;
                                "
                            >
                                {{ $record->remarks ?? '-' }}
                            </td>

                        </tr>

                    </table>

                </td>


                {{-- =====================================================
                     ORGANIZATION INFORMATION
                ====================================================== --}}

                <td
                    style="
                        width:50%;
                        vertical-align:top;
                        padding:0 0 0 10px;
                        box-sizing:border-box;
                    "
                >

                    <div
                        class="section-title"
                        style="
                            margin:0 0 12px 0;
                            padding:0;
                            font-size:10px;
                            line-height:1.2;
                            font-weight:700;
                            color:#0057ff;
                            text-transform:uppercase;
                        "
                    >
                        ORGANIZATION INFORMATION
                    </div>


                    <table
                        class="information-table"
                        style="
                            width:100%;
                            box-sizing:border-box;
                            border-collapse:collapse;
                            table-layout:fixed;
                            font-size:9px;
                        "
                    >

                        <tr>

                            <td
                                class="information-label"
                                style="
                                    width:28%;
                                    padding:0 0 7px 0;
                                    vertical-align:top;
                                    white-space:nowrap;
                                "
                            >
                                Branch
                            </td>

                            <td
                                class="information-separator"
                                style="
                                    width:4%;
                                    padding:0 0 7px 0;
                                    vertical-align:top;
                                    text-align:left;
                                "
                            >
                                :
                            </td>

                            <td
                                style="
                                    width:68%;
                                    padding:0 0 7px 2px;
                                    vertical-align:top;
                                    white-space:normal;
                                    overflow-wrap:break-word;
                                    word-wrap:break-word;
                                "
                            >
                                {{ $record->branch?->branch_name
                                    ?? $record->branch?->name
                                    ?? '-' }}
                            </td>

                        </tr>


                        <tr>

                            <td
                                class="information-label"
                                style="
                                    width:28%;
                                    padding:0 0 7px 0;
                                    vertical-align:top;
                                    white-space:nowrap;
                                "
                            >
                                Department
                            </td>

                            <td
                                class="information-separator"
                                style="
                                    width:4%;
                                    padding:0 0 7px 0;
                                    vertical-align:top;
                                    text-align:left;
                                "
                            >
                                :
                            </td>

                            <td
                                style="
                                    width:68%;
                                    padding:0 0 7px 2px;
                                    vertical-align:top;
                                    white-space:normal;
                                    overflow-wrap:break-word;
                                    word-wrap:break-word;
                                "
                            >
                                {{ $record->department?->department_name
                                    ?? $record->department?->name
                                    ?? '-' }}
                            </td>

                        </tr>


                        <tr>

                            <td
                                class="information-label"
                                style="
                                    width:28%;
                                    padding:0 0 7px 0;
                                    vertical-align:top;
                                    white-space:nowrap;
                                "
                            >
                                Warehouse
                            </td>

                            <td
                                class="information-separator"
                                style="
                                    width:4%;
                                    padding:0 0 7px 0;
                                    vertical-align:top;
                                    text-align:left;
                                "
                            >
                                :
                            </td>

                            <td
                                style="
                                    width:68%;
                                    padding:0 0 7px 2px;
                                    vertical-align:top;
                                    white-space:normal;
                                    overflow-wrap:break-word;
                                    word-wrap:break-word;
                                "
                            >
                                {{ $record->warehouse?->warehouse_name
                                    ?? $record->warehouse?->name
                                    ?? '-' }}
                            </td>

                        </tr>

                    </table>

                </td>

            </tr>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- MATERIAL REQUISITION ITEMS --}}
    {{-- ========================================================= --}}

    <div class="section">

        <div class="section-title">
            Material Requisition Items
        </div>

        <table class="items-table">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>UOM</th>
                    <th>Required Date</th>
                    <th>Warehouse</th>
                    <th>Supplier</th>
                    <th>Unit Price</th>
                    <th>Discount</th>
                    <th>Tax %</th>
                    <th>Tax Amount</th>
                    <th>Amount</th>

                </tr>

            </thead>

            <tbody>

                @forelse($record->items as $index => $item)

                 @php

                    $assignment =
                        $item->latestAssignmentMaterialRequisitionItem;

                    $supplier =
                        $assignment?->supplier?->supplier_name
                        ?? '-';

                    $quantity =
                        (float) (
                            $item->quantity
                            ?? $item->qty
                            ?? 0
                        );

                    $unitPrice =
                        (float) (
                            $assignment?->unit_price
                            ?? $item->estimated_unit_price
                            ?? 0
                        );

                    $discountPercent =
                        (float) (
                            $assignment?->discount_percent
                            ?? 0
                        );

                    $discountAmount =
                        (float) (
                            $assignment?->discount_amount
                            ?? 0
                        );

                    $taxPercent =
                        (float) (
                            $assignment?->tax_percent
                            ?? 0
                        );

                    $taxName =
                        $assignment?->tax_name
                        ?? '-';

                    $taxAmount =
                        (float) (
                            $assignment?->tax_amount
                            ?? 0
                        );

                    $grossAmount =
                        round(
                            $quantity * $unitPrice,
                            2
                        );

                    $netAmount =
                        round(
                            $grossAmount - $discountAmount,
                            2
                        );

                    $amount =
                        (float) (
                            $assignment?->grand_total
                            ?? (
                                $netAmount + $taxAmount
                            )
                        );

                @endphp

                    <tr>

                        <td class="text-center">
                            {{ $index + 1 }}
                        </td>

                        <td>
                            {{ $item->item?->item_code ?? '-' }}
                        </td>

                        <td>
                            {{ $item->item?->item_name ?? '-' }}
                        </td>

                        <td>
                            {{ $item->remarks ?? '-' }}
                        </td>

                        <td class="text-right">
                            {{ number_format($quantity, 2, '.', ',') }}
                        </td>

                        <td class="text-center">
                            {{ $item->uom?->uom_code
                                ?? $item->uom?->uom_name
                                ?? '-' }}
                        </td>

                        <td class="text-center">
                            {{ $item->required_date
                                ? \Carbon\Carbon::parse($item->required_date)->format('d-M-Y')
                                : '-' }}
                        </td>

                        <td>
                            {{ $item->warehouse?->warehouse_name ?? '-' }}
                        </td>

                        <td>
                            {{ $supplier }}
                        </td>

                        <td class="text-right">
                            {{ number_format($unitPrice, 0, ',', '.') }}
                        </td>

                        <td class="text-right">
                            @if($assignment)

                                {{ number_format($discountPercent, 2) }}%

                                @if($discountAmount > 0)
                                    <br>
                                    <span style="font-size:7px; color:#64748b;">
                                        {{ number_format($discountAmount, 2, ',', '.') }}
                                    </span>
                                @endif

                            @else
                                -
                            @endif
                        </td>

                        {{-- TAX --}}
                        <td
                            style="
                                padding:5px 3px;
                                text-align:center;
                                vertical-align:middle;
                                border:1px solid #e2e8f0;
                                line-height:1.15;
                                word-wrap:break-word;
                            "
                        >

                            @if($assignment)

                                @if($taxPercent > 0)

                                    <div
                                        style="
                                            margin-top:1px;
                                            font-size:7px;
                                            color:#64748b;
                                            line-height:1.1;
                                            text-align:center;
                                        "
                                    >
                                        {{ number_format($taxPercent, 2) }}%
                                    </div>

                                @endif

                            @else
                                -
                            @endif

                        </td>

                        <td class="text-right">
                            @if($assignment)
                                {{ number_format($taxAmount, 2, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>

                        <td class="text-right">
                            <strong>
                                {{ number_format($amount, 0, ',', '.') }}
                            </strong>
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="14"
                            style="
                                padding:20px;
                                text-align:center;
                            "
                        >
                            No items available.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- =========================================================
         MR SUMMARY
         SUBTOTAL / DISCOUNT / TAX / GRAND TOTAL
    ========================================================= --}}

    @php

        /*
        |--------------------------------------------------------------------------
        | SUMMARY SOURCE
        |--------------------------------------------------------------------------
        | Subtotal
        |     = Qty × Unit Price
        |
        | Discount
        |     = Total Discount Amount
        |
        | Tax
        |     = Total Tax Amount
        |
        | Grand Total
        |     = Subtotal - Discount + Tax
        |--------------------------------------------------------------------------
        */

        $subtotal = 0;

        $discount = 0;

        $tax = 0;

        $grandTotal = 0;


        foreach ($record->items as $summaryItem) {

            $assignmentItem =
                $summaryItem->latestAssignmentMaterialRequisitionItem;

            $qty =
                (float) (
                    $summaryItem->quantity
                    ?? $summaryItem->qty
                    ?? 0
                );

            $unitPrice =
                (float) (
                    $assignmentItem?->unit_price
                    ?? $summaryItem->estimated_unit_price
                    ?? 0
                );

            $discountAmount =
                (float) (
                    $assignmentItem?->discount_amount
                    ?? 0
                );

            $taxAmount =
                (float) (
                    $assignmentItem?->tax_amount
                    ?? 0
                );


            $subtotal +=
                $qty * $unitPrice;

            $discount +=
                $discountAmount;

            $tax +=
                $taxAmount;

        }


        $subtotal =
            round($subtotal, 2);

        $discount =
            round($discount, 2);

        $tax =
            round($tax, 2);

        $grandTotal =
            round(
                $subtotal
                - $discount
                + $tax,
                2
            );

    @endphp




    {{-- =========================================================
         SUMMARY CONTAINER
    ========================================================= --}}

    <div
        style="
            width:100%;
            margin-top:28px;
            padding:0 0 55px 0;
            box-sizing:border-box;
        "
    >

        <table
            style="
                width:300px;
                margin-left:auto;
                margin-right:28px;
                border-collapse:collapse;
                border-spacing:0;
                font-size:9px;
            "
        >

            {{-- SUBTOTAL --}}
            <tr>

                <td
                    style="
                        padding:8px 10px;
                        width:52%;
                        background:#f8f9fa;
                        border:1px solid #d7dee7;
                        font-weight:600;
                        text-align:left;
                        vertical-align:middle;
                    "
                >
                    Subtotal
                </td>

                <td
                    style="
                        padding:8px 10px;
                        width:48%;
                        border:1px solid #d7dee7;
                        text-align:right;
                        vertical-align:middle;
                        white-space:nowrap;
                    "
                >
                    Rp {{ number_format($subtotal, 2, ',', '.') }}
                </td>

            </tr>

            {{-- DISCOUNT --}}
            <tr>

                <td
                    style="
                        padding:8px 10px;
                        background:#f8f9fa;
                        border:1px solid #d7dee7;
                        font-weight:600;
                        text-align:left;
                        vertical-align:middle;
                    "
                >
                    Discount
                </td>

                <td
                    style="
                        padding:8px 10px;
                        border:1px solid #d7dee7;
                        text-align:right;
                        vertical-align:middle;
                        white-space:nowrap;
                    "
                >
                    Rp {{ number_format($discount, 2, ',', '.') }}
                </td>

            </tr>

            {{-- TAX --}}
            <tr>

                <td
                    style="
                        padding:8px 10px;
                        background:#f8f9fa;
                        border:1px solid #d7dee7;
                        font-weight:600;
                        text-align:left;
                        vertical-align:middle;
                    "
                >
                    Tax
                </td>

                <td
                    style="
                        padding:8px 10px;
                        border:1px solid #d7dee7;
                        text-align:right;
                        vertical-align:middle;
                        white-space:nowrap;
                    "
                >
                    Rp {{ number_format($tax, 2, ',', '.') }}
                </td>

            </tr>

            {{-- GRAND TOTAL --}}
            <tr>

                <td
                    style="
                        padding:9px 10px;
                        background:#2563eb;
                        color:#ffffff;
                        border:1px solid #2563eb;
                        font-weight:700;
                        text-align:left;
                        vertical-align:middle;
                    "
                >
                    Grand Total
                </td>

                <td
                    style="
                        padding:9px 10px;
                        background:#2563eb;
                        color:#ffffff;
                        border:1px solid #2563eb;
                        text-align:right;
                        vertical-align:middle;
                        font-weight:700;
                        white-space:nowrap;
                    "
                >
                    Rp {{ number_format($grandTotal, 2, ',', '.') }}
                </td>

            </tr>

        </table>

    </div>


    {{-- =========================================================
         SIGNATURE / APPROVAL AUDIT TRAIL
    ========================================================= --}}

    <div
        class="signatures"
        style="
            width:92%;
            margin:28px auto 30px auto;
        "
    >

        <table
            style="
                width:100%;
                border-collapse:collapse;
                border-spacing:0;
                table-layout:fixed;
            "
        >

            <tr>

                {{-- =================================================
                     REQUESTED BY
                ================================================== --}}

                <td
                    style="
                        width:33.3333%;
                        height:145px;
                        padding:14px 16px 16px 16px;
                        text-align:center;
                        vertical-align:top;
                        border:1px solid #d7dee7;
                        box-sizing:border-box;
                        font-size:10px;
                    "
                >

                    <div
                        style="
                            font-size:10px;
                            font-weight:700;
                            color:#111827;
                            line-height:1.2;
                            margin-bottom:20px;
                        "
                    >
                        Requested By
                    </div>


                    {{-- SUBMITTED DATE / TIME --}}

                    @if($approvalTransaction?->submitted_at)

                        <div
                            style="
                                color:#64748b;
                                font-size:9px;
                                line-height:1.4;
                                min-height:28px;
                            "
                        >
                            Submitted at
                            <br>
                            {{ $approvalTransaction->submitted_at->format('d M Y H:i:s') }}
                        </div>

                    @else

                        <div
                            style="
                                color:#94a3b8;
                                font-size:9px;
                                line-height:1.4;
                                min-height:28px;
                            "
                        >
                            -
                        </div>

                    @endif


                    {{-- SIGNATURE LINE --}}

                    <div
                        style="
                            height:30px;
                            margin:0 8px;
                            border-bottom:1px solid #9ca3af;
                        "
                    ></div>


                    {{-- REQUESTER --}}

                    <div
                        style="
                            margin-top:7px;
                            font-size:10px;
                            font-weight:500;
                            color:#111827;
                            line-height:1.25;
                        "
                    >
                        {{ $approvalTransaction?->creator?->name ?? $requester ?? '-' }}
                    </div>

                </td>


                {{-- =================================================
                     APPROVED BY #1
                ================================================== --}}

                <td
                    style="
                        width:33.3333%;
                        height:145px;
                        padding:14px 16px 16px 16px;
                        text-align:center;
                        vertical-align:top;
                        border:1px solid #d7dee7;
                        box-sizing:border-box;
                        font-size:10px;
                    "
                >

                    <div
                        style="
                            font-size:10px;
                            font-weight:700;
                            color:#111827;
                            line-height:1.2;
                            margin-bottom:20px;
                        "
                    >
                        Approved By
                    </div>


                    {{-- APPROVAL LEVEL 1 DATE / TIME --}}

                    @if(
                        $approvalStep1
                        && $approvalStep1->approver
                        && $approvalStep1->acted_at
                    )

                        <div
                            style="
                                color:#64748b;
                                font-size:9px;
                                line-height:1.4;
                                min-height:28px;
                            "
                        >
                            Approved at
                            <br>
                            {{ $approvalStep1->acted_at->format('d M Y H:i:s') }}
                        </div>

                    @else

                        <div
                            style="
                                color:#94a3b8;
                                font-size:9px;
                                line-height:1.4;
                                min-height:28px;
                            "
                        >
                            -
                        </div>

                    @endif


                    {{-- SIGNATURE LINE --}}

                    <div
                        style="
                            height:30px;
                            margin:0 8px;
                            border-bottom:1px solid #9ca3af;
                        "
                    ></div>


                    {{-- APPROVER LEVEL 1 --}}

                    <div
                        style="
                            margin-top:7px;
                            font-size:10px;
                            font-weight:500;
                            color:#111827;
                            line-height:1.25;
                        "
                    >
                        {{ $approvalStep1?->approver?->name ?? '-' }}
                    </div>

                </td>


                {{-- =================================================
                     APPROVED BY #2
                ================================================== --}}

                <td
                    style="
                        width:33.3333%;
                        height:145px;
                        padding:14px 16px 16px 16px;
                        text-align:center;
                        vertical-align:top;
                        border:1px solid #d7dee7;
                        box-sizing:border-box;
                        font-size:10px;
                    "
                >

                    <div
                        style="
                            font-size:10px;
                            font-weight:700;
                            color:#111827;
                            line-height:1.2;
                            margin-bottom:20px;
                        "
                    >
                        Approved By
                    </div>


                    {{-- APPROVAL LEVEL 2 DATE / TIME --}}

                    @if(
                        $approvalStep2
                        && $approvalStep2->approver
                        && $approvalStep2->acted_at
                    )

                        <div
                            style="
                                color:#64748b;
                                font-size:9px;
                                line-height:1.4;
                                min-height:28px;
                            "
                        >
                            Approved at
                            <br>
                            {{ $approvalStep2->acted_at->format('d M Y H:i:s') }}
                        </div>

                    @else

                        <div
                            style="
                                color:#94a3b8;
                                font-size:9px;
                                line-height:1.4;
                                min-height:28px;
                            "
                        >
                            -
                        </div>

                    @endif


                    {{-- SIGNATURE LINE --}}

                    <div
                        style="
                            height:30px;
                            margin:0 8px;
                            border-bottom:1px solid #9ca3af;
                        "
                    ></div>


                    {{-- APPROVER LEVEL 2 --}}

                    <div
                        style="
                            margin-top:7px;
                            font-size:10px;
                            font-weight:500;
                            color:#111827;
                            line-height:1.25;
                        "
                    >
                        {{ $approvalStep2?->approver?->name ?? '-' }}
                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- =========================================================
         DOCUMENT FOOTER
    ========================================================= --}}

    <div
        style="
            width:92%;
            margin:0 auto 14px auto;
        "
    >

        <table
            style="
                width:100%;
                border-collapse:collapse;
                border-spacing:0;
                table-layout:fixed;
                font-size:8px;
                color:#1f2937;
            "
        >

            {{-- =================================================
                 FOOTER ROW 1
            ================================================== --}}

            <tr>

                {{-- PRINTED ON --}}

                <td
                    style="
                        width:33.33%;
                        padding:5px 7px;
                        border:1px solid #cbd5e1;
                        vertical-align:middle;
                        text-align:center;
                        line-height:1.25;
                    "
                >

                    <strong>
                        Printed On
                    </strong>

                    <br>

                    {{ now()->format('d M Y H:i:s') }}

                </td>


                {{-- PAGE --}}

                <td
                    style="
                        width:33.33%;
                        padding:5px 7px;
                        border:1px solid #cbd5e1;
                        vertical-align:middle;
                        text-align:right;
                        line-height:1.25;
                    "
                >

                    <strong>
                        Page
                    </strong>

                    <br>

                    1 of 1

                </td>

            </tr>


            {{-- =================================================
                 FOOTER ROW 2
            ================================================== --}}

            <tr>

                {{-- GENERATED BY --}}

                <td
                    style="
                        padding:5px 7px;
                        border:1px solid #cbd5e1;
                        vertical-align:middle;
                        text-align:center;
                        line-height:1.25;
                    "
                >

                    Generated by Nexus ERP 2.0

                </td>


                {{-- VERSION --}}

                <td
                    style="
                        padding:5px 7px;
                        border:1px solid #cbd5e1;
                        vertical-align:middle;
                        text-align:right;
                        line-height:1.25;
                    "
                >

                    Version 1.0

                </td>

            </tr>

        </table>


        {{-- =================================================
             ELECTRONIC DOCUMENT DISCLAIMER
        ================================================== --}}

        <div
            style="
                margin-top:7px;
                font-size:7px;
                color:#94a3b8;
                font-style:italic;
                line-height:1.3;
                text-align:left;
            "
        >

            This Material Requisition is generated electronically by
            Nexus ERP 2.0. Printed copies are considered uncontrolled
            unless verified against the system.

        </div>

    </div>
    

</div>

</body>

</html>