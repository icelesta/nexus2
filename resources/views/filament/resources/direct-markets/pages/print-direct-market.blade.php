@php

use App\Filament\Resources\DirectMarkets\DirectMarketResource;

/*
|--------------------------------------------------------------------------
| PDF MODE
|--------------------------------------------------------------------------
|
| Preview:
|   $pdfMode = false
|
| Export PDF:
|   $pdfMode = true
|
*/

$pdfMode = $pdfMode ?? false;


/*
|--------------------------------------------------------------------------
| RECORD
|--------------------------------------------------------------------------
*/

$record = $record ?? null;


/*
|--------------------------------------------------------------------------
| COMPANY INFORMATION
|--------------------------------------------------------------------------
*/

$company = [
    'name'    => 'PT BESMINDO MATERI SEWATAMA',
    'tagline' => 'Oilfield Equipment Sales & Rental, Drilling & Work Over Rig Services',
    'service' => 'Light Vehicle & Logistics Yard Duri',
];


/*
|--------------------------------------------------------------------------
| DOCUMENT INFORMATION
|--------------------------------------------------------------------------
*/

if (! isset($documentNumber)) {
    $documentNumber = $this->getDocumentNumber();
}

if (! isset($documentDate)) {
    $documentDate = $this->getDocumentDate() ?? '-';
}

if (! isset($documentStatus)) {
    $documentStatus = $this->getDocumentStatus();
}


/*
|--------------------------------------------------------------------------
| SAFE VALUES
|--------------------------------------------------------------------------
*/

$documentNumber = $documentNumber ?: '-';

$documentDate = $documentDate ?: '-';

$documentStatus = $documentStatus ?: '-';


/*
|--------------------------------------------------------------------------
| ORGANIZATION INFORMATION
|--------------------------------------------------------------------------
*/

$companyName = $record?->company?->company_name
    ?? '-';

$businessUnit = $record?->businessUnit?->business_unit_name
    ?? '-';

$branch = $record?->branch?->branch_name
    ?? '-';

$department = $record?->department?->department_name
    ?? '-';

$section = $record?->section?->section_name
    ?? '-';

$costCenter = $record?->costCenter?->cost_center_name
    ?? '-';


/*
|--------------------------------------------------------------------------
| REQUEST INFORMATION
|--------------------------------------------------------------------------
*/

$requester = $record?->requester?->name
    ?? '-';


/*
|--------------------------------------------------------------------------
| REQUEST DATE
|--------------------------------------------------------------------------
*/

$requestDate = '-';

if ($record?->request_date) {

    $requestDate =
        $record->request_date instanceof \Carbon\CarbonInterface
            ? $record->request_date->format('d M Y')
            : \Carbon\Carbon::parse(
                $record->request_date
            )->format('d M Y');
}


/*
|--------------------------------------------------------------------------
| REQUIRED DATE
|--------------------------------------------------------------------------
*/

$requiredDate = '-';

if ($record?->required_date) {

    $requiredDate =
        $record->required_date instanceof \Carbon\CarbonInterface
            ? $record->required_date->format('d M Y')
            : \Carbon\Carbon::parse(
                $record->required_date
            )->format('d M Y');
}


/*
|--------------------------------------------------------------------------
| DELIVERY LOCATION
|--------------------------------------------------------------------------
*/

$deliveryLocation = $record?->delivery_location
    ? (string) $record->delivery_location
    : '-';


/*
|--------------------------------------------------------------------------
| PRIORITY
|--------------------------------------------------------------------------
*/

$priority = $record?->priority
    ? ucfirst((string) $record->priority)
    : '-';


/*
|--------------------------------------------------------------------------
| REFERENCE NUMBER
|--------------------------------------------------------------------------
*/

$referenceNo = $record?->reference_no
    ? (string) $record->reference_no
    : '-';


/*
|--------------------------------------------------------------------------
| REMARKS
|--------------------------------------------------------------------------
*/

$remarks = $record?->remarks
    ? (string) $record->remarks
    : '-';


/*
|--------------------------------------------------------------------------
| COMPANY LOGO
|--------------------------------------------------------------------------
*/

$logoPath = public_path(
    'images/company/icon.png'
);

$logoSrc = asset(
    'images/company/icon.png'
);

if (
    $pdfMode &&
    is_file($logoPath)
) {

    $logoSrc =
        'data:image/png;base64,' .
        base64_encode(
            file_get_contents($logoPath)
        );
}

@endphp


@if (! $pdfMode)

    <x-filament-panels::page>

@endif


<div
    class="mx-auto max-w-6xl"
    @if ($pdfMode)
        style="width:100%; margin:0; padding:0;"
    @endif
>

    <div class="mx-auto max-w-6xl">


        {{-- =========================================================
             TOOLBAR
        ========================================================== --}}

        @if (! $pdfMode)

            <div
                class="
                    mb-6
                    flex
                    items-center
                    justify-between
                    rounded-xl
                    border
                    border-gray-200
                    bg-white
                    px-4
                    py-3
                    shadow-sm
                "
            >

                {{-- =================================================
                     DOCUMENT CONTEXT
                ================================================== --}}

                <div class="min-w-0">

                    <div
                        class="
                            text-xs
                            font-semibold
                            uppercase
                            tracking-wider
                            text-gray-400
                        "
                    >
                        Direct Market
                    </div>

                    <div
                        class="
                            mt-1
                            truncate
                            text-sm
                            font-semibold
                            text-gray-800
                        "
                    >
                        {{ $documentNumber }}
                    </div>

                </div>


                {{-- =================================================
                     ACTIONS
                ================================================== --}}

                <div class="flex items-center gap-2">


                    {{-- =================================================
                         BACK
                    ================================================== --}}

                    <a
                        href="{{ DirectMarketResource::getUrl('index') }}"
                        class="
                            inline-flex
                            items-center
                            gap-2
                            rounded-lg
                            border
                            border-gray-300
                            bg-white
                            px-4
                            py-2
                            text-sm
                            font-semibold
                            text-gray-700
                            shadow-sm
                            transition
                            hover:bg-gray-50
                            hover:text-gray-900
                            focus:outline-none
                            focus:ring-2
                            focus:ring-gray-300
                        "
                    >

                        <x-filament::icon
                            icon="heroicon-o-arrow-left"
                            class="h-4 w-4"
                        />

                        <span>
                            Back
                        </span>

                    </a>


                    {{-- =================================================
                         PRINT
                    ================================================== --}}

                    <x-filament::button
                        color="gray"
                        icon="heroicon-o-printer"
                        type="button"
                        onclick="window.print()"
                    >
                        Print
                    </x-filament::button>


                    {{-- =================================================
                         EXPORT PDF
                    ================================================== --}}

                    <a
                        href="{{ route(
                            'direct-markets.print.pdf',
                            [
                                'record' =>
                                    $record->getKey(),
                            ]
                        ) }}"
                        style="
                            display:inline-flex;
                            align-items:center;
                            justify-content:center;
                            gap:8px;
                            height:36px;
                            padding:0 14px;
                            border:1px solid #2563eb;
                            border-radius:8px;
                            background:#2563eb;
                            color:#ffffff;
                            font-size:14px;
                            font-weight:600;
                            line-height:1;
                            text-decoration:none;
                            white-space:nowrap;
                            box-shadow:
                                0 1px 2px rgba(0,0,0,.05);
                        "
                    >

                        <x-filament::icon
                            icon="heroicon-o-document-arrow-down"
                            style="
                                width:16px;
                                height:16px;
                            "
                        />

                        <span>
                            Export PDF
                        </span>

                    </a>

                </div>

            </div>

        @endif


        {{-- =========================================================
             DOCUMENT
        ========================================================== --}}

        <div
            id="direct-market-preview"
            class="
                overflow-hidden
                rounded-xl
                border
                border-[#7d93aa]
                bg-white
                shadow-sm
            "
        >


            {{-- =====================================================
                 DOCUMENT HEADER
            ====================================================== --}}

            <table
                style="
                    width:100%;
                    border-collapse:collapse;
                    table-layout:fixed;
                    border-bottom:1px solid #7d93aa;
                "
            >

                <tr>


                    {{-- =================================================
                         LOGO
                    ================================================== --}}

                    <td
                        style="
                            width:100px;
                            height:140px;
                            padding:10px;
                            text-align:center;
                            vertical-align:middle;
                            border-right:1px solid #7d93aa;
                        "
                    >

                        <div
                            style="
                                width:100%;
                                height:100%;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                            "
                        >

                            <img
                                src="{{ $logoSrc }}"
                                alt="Besmindo Logo"
                                style="
                                    display:block;
                                    width:72px;
                                    height:72px;
                                    object-fit:contain;
                                    object-position:center;
                                    margin:0 auto;
                                "
                            >

                        </div>

                    </td>


                    {{-- =================================================
                         COMPANY
                    ================================================== --}}

                    <td
                        style="
                            padding:18px;
                            vertical-align:middle;
                        "
                    >

                        <div
                            style="
                                font-size:21px;
                                line-height:1.25;
                                font-weight:700;
                                color:#12385d;
                                letter-spacing:.4px;
                            "
                        >
                            {{ $company['name'] }}
                        </div>

                        <div
                            style="
                                margin-top:8px;
                                font-size:11px;
                                line-height:1.5;
                                color:#333;
                            "
                        >
                            {{ $company['tagline'] }}
                        </div>

                        <div
                            style="
                                margin-top:3px;
                                font-size:11px;
                                line-height:1.5;
                                color:#333;
                            "
                        >
                            {{ $company['service'] }}
                        </div>

                    </td>


                    {{-- =================================================
                         DOCUMENT INFORMATION
                    ================================================== --}}

                    <td
                        style="
                            width:225px;
                            padding:0;
                            vertical-align:top;
                            border-left:1px solid #7d93aa;
                        "
                    >

                        <table
                            style="
                                width:100%;
                                border-collapse:collapse;
                            "
                        >


                            {{-- DOCUMENT TITLE --}}

                            <tr>

                                <td
                                    colspan="2"
                                    style="
                                        padding:12px 8px;
                                        background:#12385d;
                                        color:#fff;
                                        text-align:center;
                                        font-size:17px;
                                        line-height:1.25;
                                        font-weight:700;
                                        border-bottom:1px solid #7d93aa;
                                    "
                                >
                                    DIRECT<br>
                                    MARKET
                                </td>

                            </tr>


                            {{-- NUMBER --}}

                            <tr>

                                <td
                                    style="
                                        width:82px;
                                        padding:8px;
                                        font-size:11px;
                                        font-weight:600;
                                        vertical-align:middle;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    Number
                                </td>

                                <td
                                    style="
                                        padding:8px;
                                        font-size:11px;
                                        font-weight:600;
                                        color:#12385d;
                                        vertical-align:middle;
                                        border-bottom:1px solid #d7dee7;
                                        word-break:break-word;
                                    "
                                >
                                    {{ $documentNumber }}
                                </td>

                            </tr>


                            {{-- STATUS --}}

                            <tr>

                                <td
                                    style="
                                        padding:8px;
                                        font-size:11px;
                                        font-weight:600;
                                        vertical-align:middle;
                                    "
                                >
                                    Status
                                </td>

                                <td
                                    style="
                                        padding:8px;
                                        font-size:11px;
                                        font-weight:700;
                                        color:#12385d;
                                        vertical-align:middle;
                                    "
                                >
                                    {{ $documentStatus }}
                                </td>

                            </tr>

                        </table>

                    </td>

                </tr>

            </table>


            {{-- =========================================================
                 REQUEST + ORGANIZATION INFORMATION
            ========================================================== --}}

            <div
                style="
                    display:grid;
                    grid-template-columns:1fr 1fr;
                    column-gap:35px;
                    padding:22px 28px;
                "
            >


                {{-- =====================================================
                     REQUEST INFORMATION
                ====================================================== --}}

                <div>

                    <div
                        style="
                            margin-bottom:13px;
                            font-size:12px;
                            font-weight:700;
                            color:#174bff;
                            letter-spacing:.3px;
                        "
                    >
                        REQUEST INFORMATION
                    </div>


                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >

                        <div>
                            Request Date
                        </div>

                        <div>
                            :
                        </div>

                        <div>
                            {{ $requestDate }}
                        </div>

                    </div>


                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >

                        <div>
                            Required Date
                        </div>

                        <div>
                            :
                        </div>

                        <div>
                            {{ $requiredDate }}
                        </div>

                    </div>


                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            font-size:11px;
                        "
                    >

                        <div>
                            Remarks
                        </div>

                        <div>
                            :
                        </div>

                        <div>
                            {{ $remarks }}
                        </div>

                    </div>

                </div>


                {{-- =====================================================
                     ORGANIZATION INFORMATION
                ====================================================== --}}

                <div>

                    <div
                        style="
                            margin-bottom:13px;
                            font-size:12px;
                            font-weight:700;
                            color:#174bff;
                            letter-spacing:.3px;
                        "
                    >
                        ORGANIZATION INFORMATION
                    </div>


                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >

                        <div>
                            Branch
                        </div>

                        <div>
                            :
                        </div>

                        <div>
                            {{ $branch }}
                        </div>

                    </div>


                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >

                        <div>
                            Department
                        </div>

                        <div>
                            :
                        </div>

                        <div>
                            {{ $department }}
                        </div>

                    </div>

                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >

                        <div>
                            Warehouse
                        </div>

                        <div>
                            :
                        </div>

                        <div>
                            {{ $record->warehouse?->warehouse_name ?? '-' }}
                        </div>

                    </div>


                </div>

            </div>


            {{-- =========================================================
                 DIRECT MARKET ITEMS
            ========================================================== --}}

            <div
                style="
                    border-top:1px solid #d7dee7;
                    padding:22px 28px;
                "
            >

                <div
                    style="
                        margin-bottom:12px;
                        font-size:12px;
                        font-weight:700;
                        color:#12385d;
                    "
                >
                    DIRECT MARKET ITEMS
                </div>


                <div
                    style="
                        overflow:hidden;
                        border:1px solid #d7dee7;
                        border-radius:6px;
                    "
                >

                    <table
                        style="
                            width:100%;
                            border-collapse:collapse;
                            font-size:9px;
                        "
                    >

                        <thead>

                            <tr
                                style="
                                    background:#f4f7fa;
                                    color:#334155;
                                "
                            >

                                <th
                                    style="
                                        width:32px;
                                        padding:7px 5px;
                                        text-align:center;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    NO
                                </th>

                                <th
                                    style="
                                        width:75px;
                                        padding:7px 5px;
                                        text-align:left;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    ITEM CODE
                                </th>

                                <th
                                    style="
                                        padding:7px 5px;
                                        text-align:left;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    ITEM NAME
                                </th>

                                <th
                                    style="
                                        padding:7px 5px;
                                        text-align:left;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    DESCRIPTION
                                </th>

                                <th
                                    style="
                                        width:55px;
                                        padding:7px 5px;
                                        text-align:right;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    QTY
                                </th>

                                <th
                                    style="
                                        width:50px;
                                        padding:7px 5px;
                                        text-align:center;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    UOM
                                </th>

                                <th
                                    style="
                                        width:85px;
                                        padding:7px 5px;
                                        text-align:center;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    REQUIRED DATE
                                </th>

                                <th
                                    style="
                                        width:120px;
                                        padding:7px 5px;
                                        text-align:left;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    DELIVERY LOCATION
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse(
                                $record->items
                                as $index => $item
                            )

                                <tr>


                                    {{-- NO --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            text-align:center;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $index + 1 }}
                                    </td>


                                    {{-- ITEM CODE --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $item->item?->item_code ?? '-' }}
                                    </td>


                                    {{-- ITEM NAME --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $item->item?->item_name ?? '-' }}
                                    </td>


                                    {{-- DESCRIPTION --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $item->remarks ?? '-' }}
                                    </td>


                                    {{-- QTY --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            text-align:right;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                        "
                                    >
                                        {{ number_format(
                                            (float) $item->qty,
                                            2
                                        ) }}
                                    </td>


                                    {{-- UOM --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            text-align:center;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $item->uom?->uom_name ?? '-' }}
                                    </td>


                                    {{-- REQUIRED DATE --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            text-align:center;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $item->required_date?->format('d-M-Y') ?? '-' }}
                                    </td>


                                    {{-- DELIVERY LOCATION --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            border-bottom:1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $item->delivery_location ?? '-' }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="8"
                                        style="
                                            padding:25px;
                                            text-align:center;
                                            color:#64748b;
                                        "
                                    >
                                        No Direct Market items available.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- =========================================================
                 APPROVAL SIGNATURE DATA
            ========================================================== --}}

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
                                (int)
                                    $step->approval_level === 1
                                &&
                                $step->status === 'APPROVED'
                        );


                $approvalStep2 =
                    $approvalTransaction
                        ?->steps
                        ->first(
                            fn ($step) =>
                                (int)
                                    $step->approval_level === 2
                                &&
                                $step->status === 'APPROVED'
                        );

            @endphp


            {{-- =========================================================
                 SIGNATURE / APPROVAL AUDIT TRAIL
            ========================================================== --}}

            <div
                style="
                    border-top:1px solid #d7dee7;
                    padding:30px 28px 35px;
                "
            >

                <table
                    style="
                        width:100%;
                        border-collapse:collapse;
                        table-layout:fixed;
                    "
                >

                    <tr>


                        {{-- =================================================
                             REQUESTED BY
                        ================================================== --}}

                        <td
                            style="
                                width:33.33%;
                                text-align:center;
                                vertical-align:top;
                                font-size:11px;
                                border:1px solid #d7dee7;
                                padding:14px 10px 16px;
                                box-sizing:border-box;
                            "
                        >

                            <strong>
                                Requested By
                            </strong>


                            @if(
                                $approvalTransaction?->submitted_at
                            )

                                <div
                                    style="
                                        margin-top:24px;
                                        color:#64748b;
                                        font-size:10px;
                                        line-height:1.4;
                                    "
                                >
                                    Submitted at
                                </div>

                                <div
                                    style="
                                        color:#64748b;
                                        font-size:10px;
                                        line-height:1.4;
                                    "
                                >
                                    {{
                                        \App\Support\Timezone\UserTimezone::format(
                                            $approvalTransaction->submitted_at,
                                            'd M Y H:i:s'
                                        )
                                    }}
                                </div>

                            @else

                                <div
                                    style="
                                        margin-top:24px;
                                        color:#94a3b8;
                                        font-size:10px;
                                    "
                                >
                                    -
                                </div>

                            @endif


                            <div
                                style="
                                    height:35px;
                                    margin:0 20px;
                                    border-bottom:1px solid #9ca3af;
                                "
                            ></div>


                            <div
                                style="
                                    margin-top:7px;
                                    font-weight:500;
                                "
                            >
                                {{
                                    $approvalTransaction
                                        ?->creator
                                        ?->name
                                    ?? '-'
                                }}
                            </div>

                        </td>


                        {{-- =================================================
                             APPROVED BY #1
                        ================================================== --}}

                        <td
                            style="
                                width:33.33%;
                                text-align:center;
                                vertical-align:top;
                                font-size:11px;
                                border:1px solid #d7dee7;
                                padding:14px 10px 16px;
                                box-sizing:border-box;
                            "
                        >

                            <strong>
                                Approved By
                            </strong>


                            @if(
                                $approvalStep1
                                &&
                                $approvalStep1->approver
                                &&
                                $approvalStep1->acted_at
                            )

                                <div
                                    style="
                                        margin-top:24px;
                                        color:#64748b;
                                        font-size:10px;
                                        line-height:1.4;
                                    "
                                >
                                    Approved at
                                </div>

                                <div
                                    style="
                                        color:#64748b;
                                        font-size:10px;
                                        line-height:1.4;
                                    "
                                >
                                    {{
                                        \App\Support\Timezone\UserTimezone::format(
                                            $approvalStep1->acted_at,
                                            'd M Y H:i:s'
                                        )
                                    }}
                                </div>

                            @else

                                <div
                                    style="
                                        margin-top:24px;
                                        color:#94a3b8;
                                        font-size:10px;
                                    "
                                >
                                    -
                                </div>

                            @endif


                            <div
                                style="
                                    height:35px;
                                    margin:0 20px;
                                    border-bottom:1px solid #9ca3af;
                                "
                            ></div>


                            <div
                                style="
                                    margin-top:7px;
                                    font-weight:500;
                                "
                            >
                                {{
                                    $approvalStep1
                                        ?->approver
                                        ?->name
                                    ?? '-'
                                }}
                            </div>

                        </td>


                        {{-- =================================================
                             APPROVED BY #2
                        ================================================== --}}

                        <td
                            style="
                                width:33.33%;
                                text-align:center;
                                vertical-align:top;
                                font-size:11px;
                                border:1px solid #d7dee7;
                                padding:14px 10px 16px;
                                box-sizing:border-box;
                            "
                        >

                            <strong>
                                Approved By
                            </strong>


                            @if(
                                $approvalStep2
                                &&
                                $approvalStep2->approver
                                &&
                                $approvalStep2->acted_at
                            )

                                <div
                                    style="
                                        margin-top:24px;
                                        color:#64748b;
                                        font-size:10px;
                                        line-height:1.4;
                                    "
                                >
                                    Approved at
                                </div>

                                <div
                                    style="
                                        color:#64748b;
                                        font-size:10px;
                                        line-height:1.4;
                                    "
                                >
                                    {{
                                        \App\Support\Timezone\UserTimezone::format(
                                            $approvalStep2->acted_at,
                                            'd M Y H:i:s'
                                        )
                                    }}
                                </div>

                            @else

                                <div
                                    style="
                                        margin-top:24px;
                                        color:#94a3b8;
                                        font-size:10px;
                                    "
                                >
                                    -
                                </div>

                            @endif


                            <div
                                style="
                                    height:35px;
                                    margin:0 20px;
                                    border-bottom:1px solid #9ca3af;
                                "
                            ></div>


                            <div
                                style="
                                    margin-top:7px;
                                    font-weight:500;
                                "
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
                 CONTROLLED DOCUMENT FOOTER
            ========================================================== --}}

            <div
                style="
                    margin-top:0;
                    padding:0 28px 18px;
                    font-size:9px;
                    color:#475569;
                "
            >

                <table
                    style="
                        width:100%;
                        border-collapse:collapse;
                        table-layout:fixed;
                    "
                >

                    <tr>

                        <td
                            style="
                                width:33.33%;
                                border:1px solid #b8c4d1;
                                padding:5px 7px;
                                text-align:center;
                                vertical-align:top;
                                line-height:1.35;
                            "
                        >

                            <div style="font-weight:700;">
                                Printed On
                            </div>

                            <div>
                                {{ now()->format('d M Y H:i:s') }}
                            </div>

                        </td>


                        <td
                            style="
                                width:33.33%;
                                border:1px solid #b8c4d1;
                                padding:5px 7px;
                                text-align:right;
                                vertical-align:top;
                                line-height:1.35;
                            "
                        >

                            <div style="font-weight:700;">
                                Page
                            </div>

                            <div>
                                1 of 1
                            </div>

                        </td>

                    </tr>


                    <tr>

                        <td
                            style="
                                border:1px solid #b8c4d1;
                                padding:5px 7px;
                                text-align:center;
                                vertical-align:top;
                                line-height:1.35;
                            "
                        >
                            Generated by Nexus ERP 2.0
                        </td>


                        <td
                            style="
                                border:1px solid #b8c4d1;
                                padding:5px 7px;
                                text-align:right;
                                vertical-align:top;
                                line-height:1.35;
                            "
                        >
                            Version 1.0
                        </td>

                    </tr>

                </table>


                <div
                    style="
                        margin-top:7px;
                        font-size:8px;
                        font-style:italic;
                        color:#94a3b8;
                        line-height:1.4;
                        text-align:center;
                    "
                >
                    This Direct Market is generated electronically by
                    Nexus ERP 2.0. Printed copies are considered uncontrolled
                    unless verified against the system.
                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     PRINT CSS
========================================================== --}}

<style>

    @media print {

        body {
            background:#fff !important;
        }

        nav,
        header,
        aside,
        [data-filament-sidebar],
        .fi-sidebar,
        .fi-topbar,
        .fi-header,
        .fi-breadcrumbs {
            display:none !important;
        }

        #direct-market-preview {
            box-shadow:none !important;
            border-radius:0 !important;
        }

        .fi-main {
            padding:0 !important;
        }

        * {
            -webkit-print-color-adjust:exact !important;
            print-color-adjust:exact !important;
        }

    }

</style>


@if (! $pdfMode)

</x-filament-panels::page>

@endif