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

        @page {
            margin: 20px 25px 25px 25px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #111827;
        }

        table {
            border-collapse: collapse;
        }

        .document {
            width: 100%;
        }

        .border {
            border: 1px solid #7d93aa;
        }

        .header-table {
            width: 100%;
            table-layout: fixed;
            border: 1px solid #7d93aa;
        }

        .logo-cell {
            width: 90px;
            height: 105px;
            text-align: center;
            vertical-align: middle;
            border-right: 1px solid #7d93aa;
        }

        .logo {
            width: 62px;
            height: 62px;
        }

        .company-cell {
            padding: 14px;
            vertical-align: middle;
        }

        .company-name {
            font-size: 17px;
            font-weight: bold;
            color: #12385d;
        }

        .company-detail {
            margin-top: 5px;
            font-size: 8px;
            line-height: 1.4;
        }

        .title-cell {
            width: 180px;
            padding: 0;
            vertical-align: top;
            border-left: 1px solid #7d93aa;
        }

        .title {
            padding: 10px;
            background: #12385d;
            color: #ffffff;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }

        .doc-info {
            width: 100%;
        }

        .doc-info td {
            padding: 6px;
            font-size: 8px;
            border-bottom: 1px solid #d7dee7;
        }

        .doc-label {
            width: 65px;
            font-weight: bold;
        }

        .doc-value {
            font-weight: bold;
            color: #12385d;
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

<div class="items-section">

    <div class="items-title">
        DIRECT MARKET ITEMS
    </div>

    <table class="items-table">

        <thead>

            <tr>

                <th style="width:4%;">
                    NO
                </th>

                <th style="width:10%;">
                    ITEM CODE
                </th>

                <th style="width:19%;">
                    ITEM NAME
                </th>

                <th style="width:19%;">
                    DESCRIPTION
                </th>

                <th style="width:7%;">
                    QTY
                </th>

                <th style="width:7%;">
                    UOM
                </th>

                <th style="width:11%;">
                    REQUIRED DATE
                </th>

                <th style="width:23%;">
                    DELIVERY LOCATION
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse ($record->items as $index => $item)

                <tr>

                    <td class="center">
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

                    <td class="right">
                        {{ number_format((float) $item->qty, 2) }}
                    </td>

                    <td class="center">
                        {{ $item->uom?->uom_name ?? '-' }}
                    </td>

                    <td class="center">

                        {{
                            $item->required_date
                                ? \Carbon\Carbon::parse(
                                    $item->required_date
                                )->format('d-M-Y')
                                : '-'
                        }}

                    </td>

                    <td>
                        {{ $item->delivery_location ?? '-' }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="8"
                        class="center"
                    >
                        No Direct Market items available.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>


{{-- =========================================================
     APPROVAL
========================================================= --}}

@php

$approvalTransaction =
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

$approvalStep1 =
    $approvalTransaction
        ?->steps
        ->first(
            fn ($step) =>
                (int) $step->approval_level === 1
                &&
                $step->status === 'APPROVED'
        );

$approvalStep2 =
    $approvalTransaction
        ?->steps
        ->first(
            fn ($step) =>
                (int) $step->approval_level === 2
                &&
                $step->status === 'APPROVED'
        );

@endphp


<div class="approval-section">

    <table class="approval-table">

        <tr>

            <td>

                <strong>
                    Requested By
                </strong>

                @if ($approvalTransaction?->submitted_at)

                    <div style="margin-top:18px;">
                        Submitted at
                    </div>

                    <div>
                        {{
                            \App\Support\Timezone\UserTimezone::format(
                                $approvalTransaction->submitted_at,
                                'd M Y H:i:s'
                            )
                        }}
                    </div>

                @endif

                <div class="signature-space"></div>

                <div class="signature-line"></div>

                <div class="signature-name">
                    {{
                        $approvalTransaction
                            ?->creator
                            ?->name
                        ?? '-'
                    }}
                </div>

            </td>


            <td>

                <strong>
                    Approved By
                </strong>

                @if (
                    $approvalStep1
                    &&
                    $approvalStep1->approver
                    &&
                    $approvalStep1->acted_at
                )

                    <div style="margin-top:18px;">
                        Approved at
                    </div>

                    <div>
                        {{
                            \App\Support\Timezone\UserTimezone::format(
                                $approvalStep1->acted_at,
                                'd M Y H:i:s'
                            )
                        }}
                    </div>

                @endif

                <div class="signature-space"></div>

                <div class="signature-line"></div>

                <div class="signature-name">
                    {{
                        $approvalStep1
                            ?->approver
                            ?->name
                        ?? '-'
                    }}
                </div>

            </td>


            <td>

                <strong>
                    Approved By
                </strong>

                @if (
                    $approvalStep2
                    &&
                    $approvalStep2->approver
                    &&
                    $approvalStep2->acted_at
                )

                    <div style="margin-top:18px;">
                        Approved at
                    </div>

                    <div>
                        {{
                            \App\Support\Timezone\UserTimezone::format(
                                $approvalStep2->acted_at,
                                'd M Y H:i:s'
                            )
                        }}
                    </div>

                @endif

                <div class="signature-space"></div>

                <div class="signature-line"></div>

                <div class="signature-name">
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