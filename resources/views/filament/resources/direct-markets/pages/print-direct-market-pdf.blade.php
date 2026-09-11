@php

$record = $record ?? null;

$documentNumber = $documentNumber ?? ($record?->dm_no ?? '-');

$documentDate = $documentDate ?? (
    $record?->request_date
        ? \Carbon\Carbon::parse($record->request_date)->format('d M Y')
        : '-'
);

$documentStatus = $documentStatus ?? strtoupper(
    (string) ($record?->status ?? '-')
);

$companyName = $record?->company?->company_name ?? '-';

$businessUnit = $record?->businessUnit?->business_unit_name ?? '-';

$branch = $record?->branch?->branch_name ?? '-';

$department = $record?->department?->department_name ?? '-';

$costCenter = $record?->costCenter?->cost_center_name ?? '-';

$requester = $record?->requester?->name ?? '-';

$requestDate = $record?->request_date
    ? \Carbon\Carbon::parse(
        $record->request_date
    )->format('d M Y')
    : '-';

$requiredDate = $record?->required_date
    ? \Carbon\Carbon::parse(
        $record->required_date
    )->format('d M Y')
    : '-';

$deliveryLocation = $record?->delivery_location
    ? (string) $record->delivery_location
    : '-';

$remarks = $record?->remarks
    ? (string) $record->remarks
    : '-';

$logoPath = public_path('images/company/icon.png');

$logoSrc = '';

if (is_file($logoPath)) {
    $logoSrc =
        'data:image/png;base64,' .
        base64_encode(
            file_get_contents($logoPath)
        );
}

@endphp

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <style>

        /*
        |--------------------------------------------------------------------------
        | PAGE
        |--------------------------------------------------------------------------
        */

        @page {
            margin: 20px 25px 25px 25px;
        }

        /*
        |--------------------------------------------------------------------------
        | BODY
        |--------------------------------------------------------------------------
        */

        body {
            margin: 0;
            padding: 0;

            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;

            color: #111827;
        }

        /*
        |--------------------------------------------------------------------------
        | GLOBAL TABLE
        |--------------------------------------------------------------------------
        */

        table {
            border-collapse: collapse;
        }

        .document {
            width: 100%;
        }

        .border {
            border: 1px solid #7d93aa;
        }
        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        |
        | Golden layout:
        |
        |   LOGO | COMPANY INFORMATION | DIRECT MARKET
        |
        |    10% |         65%         |     25%
        |
        */

        .header-table {
            width: 100%;

            table-layout: fixed;

            border: 1px solid #7d93aa;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER - LOGO
        |--------------------------------------------------------------------------
        */

        .logo-cell {
            width: 10%;

            height: 105px;

            padding: 0;

            text-align: center;
            vertical-align: middle;

            border-right: 1px solid #7d93aa;
        }

        .logo {
            width: 62px;
            height: 62px;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER - COMPANY
        |--------------------------------------------------------------------------
        */

        .company-cell {
            width: 65%;

            padding: 12px 16px;

            vertical-align: middle;
        }

        .company-name {
            font-size: 16px;

            font-weight: bold;

            color: #12385d;

            white-space: nowrap;
        }

        .company-detail {
            margin-top: 5px;

            font-size: 8px;

            line-height: 1.4;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER - DOCUMENT
        |--------------------------------------------------------------------------
        */

        .title-cell {
            width: 25%;

            padding: 0;

            vertical-align: top;

            border-left: 1px solid #7d93aa;
        }

        /*
        |--------------------------------------------------------------------------
        | DOCUMENT TITLE
        |--------------------------------------------------------------------------
        */

        .title {
            padding: 9px 6px;

            background: #12385d;

            color: #ffffff;

            text-align: center;

            font-size: 13px;

            font-weight: bold;

            line-height: 1.15;
        }


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT INFORMATION
        |--------------------------------------------------------------------------
        |
        | The label/value columns are explicitly controlled so the
        | Number and Status values stay comfortably inside the
        | right document column.
        |
        */

        .doc-info {
            width: 100%;

            table-layout: fixed;
        }

        .doc-info td {
            padding: 6px 5px;

            font-size: 7.5px;

            vertical-align: middle;

            border-bottom: 1px solid #d7dee7;
        }

        .doc-info tr:last-child td {
            border-bottom: 0;
        }

        /*
        |--------------------------------------------------------------------------
        | DOCUMENT LABEL
        |--------------------------------------------------------------------------
        */

        .doc-label {
            width: 32%;

            font-weight: bold;

            white-space: nowrap;

            text-align: left;
        }

        /*
        |--------------------------------------------------------------------------
        | DOCUMENT VALUE
        |--------------------------------------------------------------------------
        */

        .doc-value {
            width: 68%;

            padding-left: 2px !important;

            font-weight: bold;

            color: #12385d;

            text-align: left;

            white-space: nowrap;

            overflow: hidden;
        }

        .section-table {
            width: 100%;
            margin-top: 16px;
            table-layout: fixed;
        }

        .section-table > tbody > tr > td {
            width: 50%;
            vertical-align: top;
            padding: 0 12px;
        }

        .section-table > tbody > tr > td:first-child {
            padding-left: 0;
        }

        .section-table > tbody > tr > td:last-child {
            padding-right: 0;
        }

        .section-title {
            margin-bottom: 9px;
            color: #174bff;
            font-size: 9px;
            font-weight: bold;
        }

        .info-table {
            width: 100%;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 8px;
        }

        .info-label {
            width: 105px;
        }

        .info-colon {
            width: 12px;
        }

        .items-section {
            margin-top: 18px;
        }

        .items-title {
            margin-bottom: 8px;
            color: #12385d;
            font-size: 9px;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            table-layout: fixed;
            border: 1px solid #d7dee7;
        }

        .items-table th {
            padding: 6px 4px;
            background: #f4f7fa;
            color: #334155;
            font-size: 7px;
            font-weight: bold;
            border-right: 1px solid #d7dee7;
            border-bottom: 1px solid #d7dee7;
        }

        .items-table td {
            padding: 6px 4px;
            font-size: 7px;
            vertical-align: top;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .approval-section {
            margin-top: 22px;
        }

        .approval-table {
            width: 100%;
            table-layout: fixed;
        }

        .approval-table td {
            width: 33.33%;
            height: 105px;
            padding: 10px;
            text-align: center;
            vertical-align: top;
            font-size: 8px;
            border: 1px solid #d7dee7;
        }

        .signature-space {
            height: 40px;
        }

        .signature-line {
            margin: 0 15px;
            border-bottom: 1px solid #9ca3af;
        }

        .signature-name {
            margin-top: 6px;
            font-weight: bold;
        }

        .footer {
            width: 100%;
            margin-top: 18px;
            font-size: 7px;
            color: #475569;
        }

        .footer-table {
            width: 100%;
            table-layout: fixed;
        }

        .footer-table td {
            padding: 5px;
            border: 1px solid #b8c4d1;
        }

        .footer-note {
            margin-top: 6px;
            text-align: center;
            font-size: 6px;
            font-style: italic;
            color: #94a3b8;
        }

    </style>

</head>

<body>

<table class="header-table">

    <colgroup>
        <col style="width:10%;">
        <col style="width:70%;">
        <col style="width:20%;">
    </colgroup>

    <tr>

        <td class="logo-cell">

            @if ($logoSrc)

                <img
                    src="{{ $logoSrc }}"
                    class="logo"
                >

            @endif

        </td>

        <td class="company-cell">

            <div class="company-name">
                PT BESMINDO MATERI SEWATAMA
            </div>

            <div class="company-detail">
                Oilfield Equipment Sales & Rental,
                Drilling & Work Over Rig Services
            </div>

            <div class="company-detail">
                Light Vehicle & Logistics Yard Duri
            </div>

        </td>

        <td class="title-cell">

            <div class="title">
                DIRECT<br>
                MARKET
            </div>

            <table class="doc-info">

                <tr>

                    <td class="doc-label">
                        Number
                    </td>

                    <td class="doc-value">
                        {{ $documentNumber }}
                    </td>

                </tr>

                <tr>

                    <td class="doc-label">
                        Status
                    </td>

                    <td class="doc-value">
                        {{ $documentStatus }}
                    </td>

                </tr>

            </table>

        </td>

    </tr>

</table>


{{-- =========================================================
     INFORMATION
========================================================= --}}

<table class="section-table">

    <tr>

        <td>

            <div class="section-title">
                REQUEST INFORMATION
            </div>

            <table class="info-table">

                <tr>
                    <td class="info-label">Request Date</td>
                    <td class="info-colon">:</td>
                    <td>{{ $requestDate }}</td>
                </tr>

                <tr>
                    <td class="info-label">Required Date</td>
                    <td class="info-colon">:</td>
                    <td>{{ $requiredDate }}</td>
                </tr>

                <tr>
                    <td class="info-label">Remarks</td>
                    <td class="info-colon">:</td>
                    <td>{{ $remarks }}</td>
                </tr>

            </table>

        </td>

        <td>

            <div class="section-title">
                ORGANIZATION INFORMATION
            </div>

            <table class="info-table">

                <tr>
                    <td class="info-label">Branch</td>
                    <td class="info-colon">:</td>
                    <td>{{ $branch }}</td>
                </tr>

                <tr>
                    <td class="info-label">Department</td>
                    <td class="info-colon">:</td>
                    <td>{{ $department }}</td>
                </tr>

                <tr>
                    <td class="info-label">Warehouse</td>
                    <td class="info-colon">:</td>
                    <td>{{ $record->warehouse?->warehouse_name ?? '-' }}</td>
                </tr>

            </table>

        </td>

    </tr>

</table>

{{-- =========================================================
     ITEMS
========================================================= --}}

@php

    /*
    |--------------------------------------------------------------------------
    | DIRECT MARKET ITEMS
    |--------------------------------------------------------------------------
    |
    | Preview / View is the Golden Reference.
    |
    | BASIC:
    | # | ITEM CODE | ITEM NAME | ITEM REMARK | UOM |
    | REQUESTED QTY | REQUIRED DATE
    |
    | COMMERCIAL:
    | # | ITEM CODE | ITEM NAME | ITEM REMARK | UOM |
    | REQUESTED QTY | ASSIGNED QTY | SUPPLIER |
    | UNIT PRICE | DISC % | DISC AMT | AMOUNT
    |
    | DELIVERY LOCATION is intentionally excluded.
    |
    */


    /*
    |--------------------------------------------------------------------------
    | DIRECT MARKET ITEMS
    |--------------------------------------------------------------------------
    */

    $directMarketItems =
        $record?->items ?? collect();

    $directMarketItems->loadMissing([
        'item',
        'uom',
    ]);


    /*
    |--------------------------------------------------------------------------
    | LATEST ASSIGNMENT DIRECT MARKET
    |--------------------------------------------------------------------------
    */

    $assignment =
        \App\Models\AssignmentDirectMarket::query()
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


    /*
    |--------------------------------------------------------------------------
    | ADM APPROVAL TRANSACTION
    |--------------------------------------------------------------------------
    */

    $assignmentApprovalTransaction =
        $assignment
            ? \App\Models\ApprovalTransaction::query()
                ->with([
                    'steps.approver',
                ])
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


    /*
    |--------------------------------------------------------------------------
    | FINAL ADM APPROVAL
    |--------------------------------------------------------------------------
    */

    $admApproved = false;

    if (
        $assignment
        &&
        strtoupper(
            (string) $assignment->status
        ) === 'APPROVED'
        &&
        $assignmentApprovalTransaction
        &&
        strtoupper(
            (string) $assignmentApprovalTransaction->status
        ) === 'APPROVED'
    ) {

        $approvalSteps =
            $assignmentApprovalTransaction->steps;

        $totalApprovalSteps =
            $approvalSteps->count();

        $approvedApprovalSteps =
            $approvalSteps->filter(
                fn ($step) =>
                    strtoupper(
                        (string) $step->status
                    ) === 'APPROVED'
            )->count();

        $admApproved =
            $totalApprovalSteps > 0
            &&
            $approvedApprovalSteps === $totalApprovalSteps;
    }


    /*
    |--------------------------------------------------------------------------
    | ASSIGNMENT ITEMS
    |--------------------------------------------------------------------------
    */

    $assignmentItems =
        $assignment?->items
            ?->keyBy('direct_market_item_id')
        ?? collect();


    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    $totalLines =
        $directMarketItems->count();

    $totalRequestedQty =
        $directMarketItems->sum(
            fn ($item) =>
                (float) $item->qty
        );


    /*
    |--------------------------------------------------------------------------
    | COMMERCIAL TOTAL
    |--------------------------------------------------------------------------
    */

    $commercialAmount = 0;

    if ($admApproved) {

        $commercialAmount =
            $assignmentItems->sum(
                fn ($assignmentItem) =>
                    (float) (
                        $assignmentItem->grand_total
                        ?? 0
                    )
            );
    }

@endphp


<div class="items-section">

    <div class="items-title">
        DIRECT MARKET ITEMS
    </div>


    <table
        class="items-table"
        style="
            width:100%;
            table-layout:fixed;
            border:1px solid #d7dee7;
        "
    >

        <thead>

            <tr>

                {{-- =====================================================
                     NO
                ====================================================== --}}

                <th
                    style="
                        width:4%;
                        padding:6px 2px;
                        text-align:center;
                        vertical-align:middle;
                        white-space:nowrap;
                        border:1px solid #d7dee7;
                    "
                >
                    NO
                </th>


                {{-- =====================================================
                     ITEM CODE
                ====================================================== --}}

                <th
                    style="
                        width:8%;
                        padding:6px 3px;
                        text-align:center;
                        vertical-align:middle;
                        white-space:nowrap;
                        border:1px solid #d7dee7;
                    "
                >
                    ITEM CODE
                </th>


                {{-- =====================================================
                     ITEM NAME
                ====================================================== --}}

                <th
                    style="
                        width:14%;
                        padding:6px 3px;
                        text-align:center;
                        vertical-align:middle;
                        border:1px solid #d7dee7;
                    "
                >
                    ITEM NAME
                </th>


                {{-- =====================================================
                     ITEM REMARK
                ====================================================== --}}

                <th
                    style="
                        width:14%;
                        padding:6px 3px;
                        text-align:center;
                        vertical-align:middle;
                        border:1px solid #d7dee7;
                    "
                >
                    ITEM REMARK
                </th>


                {{-- =====================================================
                     UOM
                ====================================================== --}}

                <th
                    style="
                        width:5%;
                        padding:6px 2px;
                        text-align:center;
                        vertical-align:middle;
                        white-space:nowrap;
                        border:1px solid #d7dee7;
                    "
                >
                    UOM
                </th>


                @if (! $admApproved)

                    {{-- =================================================
                         REQUESTED QTY
                    ================================================== --}}

                    <th
                        style="
                            width:16%;
                            padding:5px 2px;
                            text-align:center;
                            vertical-align:middle;
                            border:1px solid #d7dee7;
                            line-height:1.15;
                        "
                    >
                        REQUESTED<br>
                        QTY
                    </th>


                    {{-- =================================================
                         REQUIRED DATE
                    ================================================== --}}

                    <th
                        style="
                            width:39%;
                            padding:6px 3px;
                            text-align:center;
                            vertical-align:middle;
                            border:1px solid #d7dee7;
                        "
                    >
                        REQUIRED DATE
                    </th>

                @else

                    {{-- =================================================
                         REQUESTED QTY
                    ================================================== --}}

                    <th
                        style="
                            width:8%;
                            padding:5px 2px;
                            text-align:center;
                            vertical-align:middle;
                            border:1px solid #d7dee7;
                            line-height:1.15;
                        "
                    >
                        REQUESTED<br>
                        QTY
                    </th>


                    {{-- =================================================
                         ASSIGNED QTY
                    ================================================== --}}

                    <th
                        style="
                            width:8%;
                            padding:5px 2px;
                            text-align:center;
                            vertical-align:middle;
                            border:1px solid #d7dee7;
                            line-height:1.15;
                        "
                    >
                        ASSIGNED<br>
                        QTY
                    </th>


                    {{-- =================================================
                         SUPPLIER
                    ================================================== --}}

                    <th
                        style="
                            width:10%;
                            padding:6px 3px;
                            text-align:center;
                            vertical-align:middle;
                            border:1px solid #d7dee7;
                        "
                    >
                        SUPPLIER
                    </th>


                    {{-- =================================================
                         UNIT PRICE
                    ================================================== --}}

                    <th
                        style="
                            width:8%;
                            padding:6px 2px;
                            text-align:center;
                            vertical-align:middle;
                            white-space:nowrap;
                            border:1px solid #d7dee7;
                        "
                    >
                        UNIT PRICE
                    </th>


                    {{-- =================================================
                         DISC %
                    ================================================== --}}

                    <th
                        style="
                            width:5%;
                            padding:6px 2px;
                            text-align:center;
                            vertical-align:middle;
                            white-space:nowrap;
                            border:1px solid #d7dee7;
                        "
                    >
                        DISC %
                    </th>


                    {{-- =================================================
                         DISC AMT
                    ================================================== --}}

                    <th
                        style="
                            width:7%;
                            padding:6px 2px;
                            text-align:center;
                            vertical-align:middle;
                            white-space:nowrap;
                            border:1px solid #d7dee7;
                        "
                    >
                        DISC AMT
                    </th>


                    {{-- =================================================
                         AMOUNT
                    ================================================== --}}

                    <th
                        style="
                            width:9%;
                            padding:6px 2px;
                            text-align:center;
                            vertical-align:middle;
                            white-space:nowrap;
                            border:1px solid #d7dee7;
                            border-left:1px solid #9caabd;
                            border-right:1px solid #9caabd;
                        "
                    >
                        AMOUNT
                    </th>

                @endif

            </tr>

        </thead>


        <tbody>

            @forelse (
                $directMarketItems
                as $index => $item
            )

                @php

                    $assignmentItem =
                        $assignmentItems->get(
                            $item->getKey()
                        );

                @endphp


                <tr>

                    {{-- =================================================
                         NO
                    ================================================== --}}

                    <td
                        class="center"
                        style="
                            border:1px solid #e2e8f0;
                        "
                    >
                        {{ $index + 1 }}
                    </td>


                    {{-- =================================================
                         ITEM CODE
                    ================================================== --}}

                    <td
                        style="
                            border:1px solid #e2e8f0;
                        "
                    >
                        {{ $item->item?->item_code ?? '-' }}
                    </td>


                    {{-- =================================================
                         ITEM NAME
                    ================================================== --}}

                    <td
                        style="
                            border:1px solid #e2e8f0;
                        "
                    >
                        {{ $item->item?->item_name ?? '-' }}
                    </td>


                    {{-- =================================================
                         ITEM REMARK
                    ================================================== --}}

                    <td
                        style="
                            border:1px solid #e2e8f0;
                        "
                    >
                        {{ $item->remarks ?? '-' }}
                    </td>


                    {{-- =================================================
                         UOM
                    ================================================== --}}

                    <td
                        class="center"
                        style="
                            border:1px solid #e2e8f0;
                        "
                    >
                        {{ $item->uom?->uom_name ?? '-' }}
                    </td>


                    @if (! $admApproved)

                        {{-- =================================================
                             REQUESTED QTY
                        ================================================== --}}

                        <td
                            class="right"
                            style="
                                border:1px solid #e2e8f0;
                                white-space:nowrap;
                            "
                        >
                            {{ number_format(
                                (float) $item->qty,
                                2
                            ) }}
                        </td>


                        {{-- =================================================
                             REQUIRED DATE
                        ================================================== --}}

                        <td
                            class="center"
                            style="
                                border:1px solid #e2e8f0;
                                white-space:nowrap;
                            "
                        >
                            {{
                                $item->required_date
                                    ? \Carbon\Carbon::parse(
                                        $item->required_date
                                    )->format('d-M-Y')
                                    : '-'
                            }}
                        </td>

                    @else

                        {{-- =================================================
                             REQUESTED QTY
                        ================================================== --}}

                        <td
                            class="right"
                            style="
                                border:1px solid #e2e8f0;
                                white-space:nowrap;
                            "
                        >
                            {{ number_format(
                                (float) $item->qty,
                                2
                            ) }}
                        </td>


                        {{-- =================================================
                             ASSIGNED QTY
                        ================================================== --}}

                        <td
                            class="right"
                            style="
                                border:1px solid #e2e8f0;
                                white-space:nowrap;
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


                        {{-- =================================================
                             SUPPLIER
                        ================================================== --}}

                        <td
                            style="
                                border:1px solid #e2e8f0;
                            "
                        >
                            {{
                                $assignmentItem
                                    ?->supplier
                                    ?->supplier_name
                                ?? '-'
                            }}
                        </td>


                        {{-- =================================================
                             UNIT PRICE
                        ================================================== --}}

                        <td
                            class="right"
                            style="
                                border:1px solid #e2e8f0;
                                white-space:nowrap;
                            "
                        >
                            {{ number_format(
                                (float) (
                                    $assignmentItem?->unit_price
                                    ?? 0
                                ),
                                2
                            ) }}
                        </td>


                        {{-- =================================================
                             DISC %
                        ================================================== --}}

                        <td
                            class="right"
                            style="
                                border:1px solid #e2e8f0;
                                white-space:nowrap;
                            "
                        >
                            {{ number_format(
                                (float) (
                                    $assignmentItem?->discount_percent
                                    ?? 0
                                ),
                                2
                            ) }}%
                        </td>


                        {{-- =================================================
                             DISC AMT
                        ================================================== --}}

                        <td
                            class="right"
                            style="
                                border:1px solid #e2e8f0;
                                white-space:nowrap;
                            "
                        >
                            {{ number_format(
                                (float) (
                                    $assignmentItem?->discount_amount
                                    ?? 0
                                ),
                                2
                            ) }}
                        </td>


                        {{-- =================================================
                             AMOUNT
                        ================================================== --}}

                        <td
                            class="right"
                            style="
                                border:1px solid #e2e8f0;
                                border-left:1px solid #9caabd;
                                border-right:1px solid #9caabd;
                                white-space:nowrap;
                            "
                        >
                            {{ number_format(
                                (float) (
                                    $assignmentItem?->grand_total
                                    ?? 0
                                ),
                                2
                            ) }}
                        </td>

                    @endif

                </tr>

            @empty

                <tr>

                    <td
                        colspan="{{ $admApproved ? 12 : 7 }}"
                        class="center"
                        style="
                            border:1px solid #d7dee7;
                        "
                    >
                        No Direct Market items available.
                    </td>

                </tr>

            @endforelse

        </tbody>


        {{-- =============================================================
             TOTAL
        ============================================================= --}}

        @if ($totalLines > 0)

            <tfoot>

                <tr>

                    {{-- TOTAL LABEL --}}

                    <td
                        colspan="5"
                        class="right"
                        style="
                            padding:6px 4px;
                            font-weight:bold;
                            border:1px solid #d7dee7;
                            background:#f4f7fa;
                        "
                    >
                        TOTAL
                    </td>


                    @if (! $admApproved)

                        {{-- REQUESTED QTY TOTAL --}}

                        <td
                            class="right"
                            style="
                                padding:6px 4px;
                                font-weight:bold;
                                border:1px solid #d7dee7;
                                background:#f4f7fa;
                                white-space:nowrap;
                            "
                        >
                            {{ number_format(
                                $totalRequestedQty,
                                2
                            ) }}
                        </td>


                        {{-- REQUIRED DATE EMPTY --}}

                        <td
                            style="
                                border:1px solid #d7dee7;
                                background:#f4f7fa;
                            "
                        ></td>

                    @else

                        {{-- REQUESTED QTY TOTAL --}}

                        <td
                            class="right"
                            style="
                                padding:6px 3px;
                                font-weight:bold;
                                border:1px solid #d7dee7;
                                background:#f4f7fa;
                                white-space:nowrap;
                            "
                        >
                            {{ number_format(
                                $totalRequestedQty,
                                2
                            ) }}
                        </td>


                        {{-- ASSIGNED QTY TOTAL --}}

                        <td
                            class="right"
                            style="
                                padding:6px 3px;
                                font-weight:bold;
                                border:1px solid #d7dee7;
                                background:#f4f7fa;
                                white-space:nowrap;
                            "
                        >
                            {{ number_format(
                                $assignmentItems->sum(
                                    fn ($assignmentItem) =>
                                        (float) (
                                            $assignmentItem
                                                ->assigned_qty
                                            ?? 0
                                        )
                                ),
                                2
                            ) }}
                        </td>


                        {{-- SUPPLIER + COMMERCIAL COLUMNS --}}

                        <td
                            colspan="5"
                            style="
                                border:1px solid #d7dee7;
                                background:#f4f7fa;
                            "
                        ></td>


                        {{-- AMOUNT TOTAL --}}

                        <td
                            class="right"
                            style="
                                padding:6px 3px;
                                font-weight:bold;
                                border:1px solid #d7dee7;
                                border-left:1px solid #9caabd;
                                border-right:1px solid #9caabd;
                                background:#f4f7fa;
                                white-space:nowrap;
                            "
                        >
                            {{ number_format(
                                $commercialAmount,
                                2
                            ) }}
                        </td>

                    @endif

                </tr>

            </tfoot>

        @endif

    </table>

</div>

{{-- =========================================================
     APPROVAL / SIGNATURE
========================================================= --}}

@php

    /*
    |--------------------------------------------------------------------------
    | DIRECT MARKET APPROVAL TRANSACTION
    |--------------------------------------------------------------------------
    |
    | Level 1 belongs to Direct Market.
    |
    */

    $directMarketApprovalTransaction =
        \App\Models\ApprovalTransaction::query()
            ->with([
                'creator',
                'steps.approver',
            ])
            ->where(
                'document_type',
                'DIRECT_MARKET'
            )
            ->where(
                'document_id',
                $record->getKey()
            )
            ->latest('id')
            ->first();


    /*
    |--------------------------------------------------------------------------
    | DIRECT MARKET APPROVAL LEVEL 1
    |--------------------------------------------------------------------------
    |
    | Find the latest acted Level 1 step.
    |
    | APPROVED / REJECTED are displayed.
    | Pending / unacted remains "-".
    |
    */

    $approvalStep1 =
        $directMarketApprovalTransaction
            ?->steps
            ->filter(
                fn ($step) =>
                    (int) $step->approval_level === 1
                    &&
                    in_array(
                        strtoupper(
                            (string) $step->status
                        ),
                        [
                            'APPROVED',
                            'REJECTED',
                        ],
                        true
                    )
            )
            ->sortByDesc('id')
            ->first();


    /*
    |--------------------------------------------------------------------------
    | LATEST ASSIGNMENT DIRECT MARKET
    |--------------------------------------------------------------------------
    |
    | Level 2 belongs to ADM.
    |
    */

    $assignment =
        \App\Models\AssignmentDirectMarket::query()
            ->where(
                'direct_market_id',
                $record->getKey()
            )
            ->latest('id')
            ->first();


    /*
    |--------------------------------------------------------------------------
    | ASSIGNMENT DIRECT MARKET APPROVAL TRANSACTION
    |--------------------------------------------------------------------------
    */

    $assignmentApprovalTransaction =
        $assignment
            ? \App\Models\ApprovalTransaction::query()
                ->with([
                    'steps.approver',
                ])
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


    /*
    |--------------------------------------------------------------------------
    | ASSIGNMENT DIRECT MARKET APPROVAL LEVEL 2
    |--------------------------------------------------------------------------
    |
    | Only an acted Level 2 step is shown.
    |
    | APPROVED / REJECTED are displayed.
    | Pending / unacted remains "-".
    |
    */

    $approvalStep2 =
        $assignmentApprovalTransaction
            ?->steps
            ->filter(
                fn ($step) =>
                    (int) $step->approval_level === 2
                    &&
                    in_array(
                        strtoupper(
                            (string) $step->status
                        ),
                        [
                            'APPROVED',
                            'REJECTED',
                        ],
                        true
                    )
            )
            ->sortByDesc('id')
            ->first();


    /*
    |--------------------------------------------------------------------------
    | APPROVAL DISPLAY HELPERS
    |--------------------------------------------------------------------------
    */

    $approvalStep1Status =
        $approvalStep1
            ? strtoupper(
                (string) $approvalStep1->status
            )
            : null;


    $approvalStep2Status =
        $approvalStep2
            ? strtoupper(
                (string) $approvalStep2->status
            )
            : null;


    /*
    |--------------------------------------------------------------------------
    | STATUS CLASS
    |--------------------------------------------------------------------------
    */

    $approvalStatusClass = function (
        ?string $status
    ): string {

        return $status === 'REJECTED'
            ? 'approval-rejected'
            : 'approval-approved';
    };


    /*
    |--------------------------------------------------------------------------
    | STATUS LABEL
    |--------------------------------------------------------------------------
    */

    $approvalStatusLabel = function (
        ?string $status
    ): string {

        return in_array(
            $status,
            [
                'APPROVED',
                'REJECTED',
            ],
            true
        )
            ? $status
            : '-';
    };

@endphp


<div class="approval-section">

    <table class="approval-table">

        <tr>

            {{-- =========================================================
                 REQUESTED BY
            ========================================================== --}}

            <td>

                <strong>
                    Requested By
                </strong>


                @if (
                    $directMarketApprovalTransaction
                    ?->submitted_at
                )

                    <div style="margin-top:18px;">
                        Submitted at
                    </div>

                    <div>
                        {{
                            \App\Support\Timezone\UserTimezone::format(
                                $directMarketApprovalTransaction->submitted_at,
                                'd M Y H:i:s'
                            )
                        }}
                    </div>

                @else

                    <div style="margin-top:18px;">
                        -
                    </div>

                @endif


                <div class="signature-space"></div>

                <div class="signature-line"></div>


                <div class="signature-name">

                    {{
                        $directMarketApprovalTransaction
                            ?->creator
                            ?->name
                        ?? '-'
                    }}

                </div>

            </td>


            {{-- =========================================================
                 APPROVAL LEVEL 1
            ========================================================== --}}

            <td>

                <strong>
                    Approved By
                </strong>


                @if ($approvalStep1)

                    <div
                        style="
                            margin-top:18px;
                            color:
                                {{
                                    $approvalStep1Status === 'REJECTED'
                                        ? '#dc2626'
                                        : '#64748b'
                                }};
                        "
                    >

                        {{
                            $approvalStep1Status === 'REJECTED'
                                ? 'Rejected at'
                                : 'Approved at'
                        }}

                    </div>


                    @if ($approvalStep1->acted_at)

                        <div
                            style="
                                color:
                                    {{
                                        $approvalStep1Status === 'REJECTED'
                                            ? '#dc2626'
                                            : '#64748b'
                                    }};
                            "
                        >
                            {{
                                \App\Support\Timezone\UserTimezone::format(
                                    $approvalStep1->acted_at,
                                    'd M Y H:i:s'
                                )
                            }}
                        </div>

                    @endif


                    <div
                        class="{{
                            $approvalStatusClass(
                                $approvalStep1Status
                            )
                        }}"
                        style="margin-top:2px;"
                    >
                        {{
                            $approvalStatusLabel(
                                $approvalStep1Status
                            )
                        }}
                    </div>

                @else

                    <div style="margin-top:18px;">
                        -
                    </div>

                @endif


                <div class="signature-space"></div>

                <div class="signature-line"></div>


                <div
                    class="signature-name"
                    {{
                        $approvalStep1Status === 'REJECTED'
                            ? 'style=color:#dc2626;'
                            : ''
                    }}
                >

                    {{
                        $approvalStep1
                            ?->approver
                            ?->name
                        ?? '-'
                    }}

                </div>

            </td>


            {{-- =========================================================
                 APPROVAL LEVEL 2
            ========================================================== --}}

            <td>

                <strong>
                    Approved By
                </strong>


                @if ($approvalStep2)

                    <div
                        style="
                            margin-top:18px;
                            color:
                                {{
                                    $approvalStep2Status === 'REJECTED'
                                        ? '#dc2626'
                                        : '#64748b'
                                }};
                        "
                    >

                        {{
                            $approvalStep2Status === 'REJECTED'
                                ? 'Rejected at'
                                : 'Approved at'
                        }}

                    </div>


                    @if ($approvalStep2->acted_at)

                        <div
                            style="
                                color:
                                    {{
                                        $approvalStep2Status === 'REJECTED'
                                            ? '#dc2626'
                                            : '#64748b'
                                    }};
                            "
                        >

                            {{
                                \App\Support\Timezone\UserTimezone::format(
                                    $approvalStep2->acted_at,
                                    'd M Y H:i:s'
                                )
                            }}

                        </div>

                    @endif


                    <div
                        class="{{
                            $approvalStatusClass(
                                $approvalStep2Status
                            )
                        }}"
                        style="margin-top:2px;"
                    >

                        {{
                            $approvalStatusLabel(
                                $approvalStep2Status
                            )
                        }}

                    </div>

                @else

                    <div style="margin-top:18px;">
                        -
                    </div>

                @endif


                <div class="signature-space"></div>

                <div class="signature-line"></div>


                <div
                    class="signature-name"
                    {{
                        $approvalStep2Status === 'REJECTED'
                            ? 'style=color:#dc2626;'
                            : ''
                    }}
                >

                    {{
                        $approvalStep2
                            ?->approver
                            ?->name
                        ?? '-'
                    }}

                </div>

            </td>

        </tr>

    </table>

</div>


{{-- =========================================================
     FOOTER
========================================================= --}}

<div class="footer">

    <table class="footer-table">

        <tr>

            <td style="text-align:center;">
                <strong>Printed On</strong><br>
                {{ now()->format('d M Y H:i:s') }}
            </td>

            <td style="text-align:right;">
                <strong>Page</strong><br>
                1 of 1
            </td>

        </tr>

        <tr>

            <td style="text-align:center;">
                Generated by Nexus ERP 2.0
            </td>

            <td style="text-align:right;">
                Version 1.0
            </td>

        </tr>

    </table>

    <div class="footer-note">
        This Direct Market is generated electronically by
        Nexus ERP 2.0. Printed copies are considered uncontrolled
        unless verified against the system.
    </div>

</div>

</body>

</html>