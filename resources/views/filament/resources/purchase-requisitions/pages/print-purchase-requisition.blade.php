@php

use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;

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
| IMPORTANT:
| Jangan bergantung kepada $this ketika PDF mode,
| karena Dompdf merender Blade melalui loadView().
|--------------------------------------------------------------------------
*/
$pdfMode = $pdfMode ?? false;


/*
|--------------------------------------------------------------------------
| RECORD
|--------------------------------------------------------------------------
|
| Preview:
|   record berasal dari Livewire page.
|
| PDF:
|   record dikirim langsung melalui Pdf::loadView().
|--------------------------------------------------------------------------
*/
$record = $record ?? ($this->record ?? null);


/*
|--------------------------------------------------------------------------
| Company Information
|--------------------------------------------------------------------------
*/
$company = [
    'name'    => 'PT BESMINDO MATERI SEWATAMA',
    'tagline' => 'Oilfield Equipment Sales & Rental, Drilling & Work Over Rig Services',
    'service' => 'Light Vehicle & Logistics Yard Duri',
];


/*
|--------------------------------------------------------------------------
| Document Information
|--------------------------------------------------------------------------
|
| Pada Preview, helper berasal dari Livewire Page.
| Pada PDF, value dikirim langsung dari exportPdf().
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
| Safe Document Values
|--------------------------------------------------------------------------
*/
$documentNumber = $documentNumber ?: '-';

$documentDate = $documentDate ?: '-';

$documentStatus = $documentStatus ?: '-';


/*
|--------------------------------------------------------------------------
| Safe Organization Values
|--------------------------------------------------------------------------
*/
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

$warehouse = $record?->warehouse?->warehouse_name
    ?? '-';


/*
|--------------------------------------------------------------------------
| Request Information
|--------------------------------------------------------------------------
*/
$requester = $record?->requester?->name
    ?? '-';


/*
|--------------------------------------------------------------------------
| Request Date
|--------------------------------------------------------------------------
*/
$requestDate = '-';

if ($record?->request_date) {
    $requestDate = $record->request_date instanceof \Carbon\CarbonInterface
        ? $record->request_date->format('d M Y')
        : \Carbon\Carbon::parse($record->request_date)->format('d M Y');
}


/*
|--------------------------------------------------------------------------
| Required Date
|--------------------------------------------------------------------------
*/
$requiredDate = '-';

if ($record?->required_date) {
    $requiredDate = $record->required_date instanceof \Carbon\CarbonInterface
        ? $record->required_date->format('d M Y')
        : \Carbon\Carbon::parse($record->required_date)->format('d M Y');
}


/*
|--------------------------------------------------------------------------
| Priority
|--------------------------------------------------------------------------
*/
$priority = $record?->priority
    ? ucfirst((string) $record->priority)
    : '-';


/*
|--------------------------------------------------------------------------
| Delivery Location
|--------------------------------------------------------------------------
*/
$deliveryLocation = $record?->delivery_location
    ? (string) $record->delivery_location
    : '-';


/*
|--------------------------------------------------------------------------
| Reference Number
|--------------------------------------------------------------------------
*/
$referenceNo = $record?->reference_no
    ? (string) $record->reference_no
    : '-';


/*
|--------------------------------------------------------------------------
| Remarks
|--------------------------------------------------------------------------
*/
$remarks = $record?->remarks
    ? (string) $record->remarks
    : '-';


/*
|--------------------------------------------------------------------------
| Company Logo
|--------------------------------------------------------------------------
|
| Browser:
|   asset() URL
|
| PDF:
|   local file converted to Base64
|
|--------------------------------------------------------------------------
*/
$logoPath = public_path('images/company/icon.png');

$logoSrc = asset('images/company/icon.png');

if ($pdfMode && is_file($logoPath)) {
    $logoSrc = 'data:image/png;base64,' . base64_encode(
        file_get_contents($logoPath)
    );
}


/*
|--------------------------------------------------------------------------
| PDF / Print Helper
|--------------------------------------------------------------------------
*/
$printClass = $pdfMode
    ? 'purchase-requisition-pdf'
    : 'purchase-requisition-preview';

@endphp


<x-filament-panels::page>

    <div class="mx-auto max-w-6xl">

        {{-- =========================================================
             TOOLBAR
        ========================================================== --}}
        @if (! $pdfMode)

            <div class="mb-6 flex items-center justify-between">

                <div>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $documentNumber }}
                        <span class="mx-1">•</span>
                        Preview
                    </p>
                </div>

                <div class="flex items-center gap-3">

                    {{-- =========================================================
                         BACK TO MATERIAL REQUISITION LIST
                    ========================================================== --}}
                    <x-filament::button
                        color="gray"
                        icon="heroicon-o-arrow-left"
                        :href="PurchaseRequisitionResource::getUrl('index')"
                    >
                        Back
                    </x-filament::button>


                    {{-- =========================================================
                         PRINT
                    ========================================================== --}}
                    <x-filament::button
                        color="warning"
                        icon="heroicon-o-printer"
                        type="button"
                        onclick="window.print()"
                    >
                        Print
                    </x-filament::button>


                    {{-- =========================================================
                         EXPORT PDF
                         TEMPORARILY DISABLED
                    ========================================================== --}}
                    <x-filament::button
                        color="success"
                        icon="heroicon-o-document-arrow-down"
                        disabled
                    >
                        Export PDF
                    </x-filament::button>

                </div>

            </div>

        @endif


        {{-- =========================================================
             DOCUMENT
        ========================================================== --}}
        <div
            id="material-requisition-preview"
            class="overflow-hidden rounded-xl border border-[#7d93aa] bg-white shadow-sm"
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
                         COMPANY INFORMATION
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
                                    MATERIAL<br>
                                    REQUISITION
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


                            {{-- DATE --}}
                            <tr>

                                <td
                                    style="
                                        padding:8px;
                                        font-size:11px;
                                        font-weight:600;
                                        vertical-align:middle;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    Date
                                </td>

                                <td
                                    style="
                                        padding:8px;
                                        font-size:11px;
                                        vertical-align:middle;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    {{ $documentDate }}
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


                    {{-- Requester --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >
                        <div>Requester</div>
                        <div>:</div>
                        <div>{{ $requester }}</div>
                    </div>


                    {{-- Request Date --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >
                        <div>Request Date</div>
                        <div>:</div>
                        <div>{{ $requestDate }}</div>
                    </div>


                    {{-- Required Date --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >
                        <div>Required Date</div>
                        <div>:</div>
                        <div>{{ $requiredDate }}</div>
                    </div>


                    {{-- Priority --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >
                        <div>Priority</div>
                        <div>:</div>
                        <div>{{ $priority }}</div>
                    </div>


                    {{-- Delivery Location --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >
                        <div>Delivery Location</div>
                        <div>:</div>
                        <div>{{ $deliveryLocation }}</div>
                    </div>


                    {{-- Reference --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >
                        <div>Reference No</div>
                        <div>:</div>
                        <div>{{ $referenceNo }}</div>
                    </div>


                    {{-- Remarks --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            font-size:11px;
                        "
                    >
                        <div>Remarks</div>
                        <div>:</div>
                        <div>{{ $remarks }}</div>
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


                    {{-- Company --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >
                        <div>Company</div>
                        <div>:</div>
                        <div>{{ $record->company?->company_name ?? '-' }}</div>
                    </div>


                    {{-- Business Unit --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >
                        <div>Business Unit</div>
                        <div>:</div>
                        <div>{{ $businessUnit }}</div>
                    </div>


                    {{-- Branch --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >
                        <div>Branch</div>
                        <div>:</div>
                        <div>{{ $branch }}</div>
                    </div>


                    {{-- Department --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >
                        <div>Department</div>
                        <div>:</div>
                        <div>{{ $department }}</div>
                    </div>


                    {{-- Section --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >
                        <div>Section</div>
                        <div>:</div>
                        <div>{{ $section }}</div>
                    </div>


                    {{-- Cost Center --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            margin-bottom:10px;
                            font-size:11px;
                        "
                    >
                        <div>Cost Center</div>
                        <div>:</div>
                        <div>{{ $costCenter }}</div>
                    </div>


                    {{-- Warehouse --}}
                    <div
                        style="
                            display:grid;
                            grid-template-columns:145px 15px 1fr;
                            font-size:11px;
                        "
                    >
                        <div>Warehouse</div>
                        <div>:</div>
                        <div>{{ $warehouse }}</div>
                    </div>

                </div>

            </div>


            {{-- =========================================================
                 ITEMS
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
                    MATERIAL REQUISITION ITEMS
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
                                        width:28px;
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
                                        width:70px;
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
                                        width:45px;
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
                                        width:45px;
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
                                        width:70px;
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
                                        width:85px;
                                        padding:7px 5px;
                                        text-align:left;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    WAREHOUSE
                                </th>

                                {{-- =====================================================
                                     PURCHASING INFORMATION
                                ====================================================== --}}

                                <th
                                    style="
                                        width:105px;
                                        padding:7px 5px;
                                        text-align:left;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    SUPPLIER
                                </th>

                                <th
                                    style="
                                        width:75px;
                                        padding:7px 5px;
                                        text-align:right;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    UNIT PRICE
                                </th>

                                <th
                                    style="
                                        width:65px;
                                        padding:7px 5px;
                                        text-align:right;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    DISCOUNT
                                </th>

                                <th
                                    style="
                                        width:65px;
                                        padding:7px 5px;
                                        text-align:center;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    TAX
                                </th>

                                <th
                                    style="
                                        width:75px;
                                        padding:7px 5px;
                                        text-align:right;
                                        border-right:1px solid #d7dee7;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    TAX AMOUNT
                                </th>

                                <th
                                    style="
                                        width:85px;
                                        padding:7px 5px;
                                        text-align:right;
                                        border-bottom:1px solid #d7dee7;
                                    "
                                >
                                    AMOUNT
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($record->items as $index => $item)

                                @php
                                    $assignmentItem =
                                        $item->latestAssignmentMaterialRequisitionItem;
                                @endphp

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
                                        {{ number_format((float) $item->quantity, 2) }}
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

                                    {{-- WAREHOUSE --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $item->warehouse?->warehouse_name ?? '-' }}
                                    </td>

                                    {{-- =================================================
                                         SUPPLIER
                                    ================================================== --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $assignmentItem?->supplier?->supplier_name ?? '-' }}
                                    </td>

                                    {{-- =================================================
                                         UNIT PRICE
                                    ================================================== --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            text-align:right;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                            white-space:nowrap;
                                        "
                                    >
                                        @if($assignmentItem)
                                            Rp {{ number_format((float) $assignmentItem->unit_price, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- =================================================
                                         DISCOUNT
                                    ================================================== --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            text-align:right;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                            white-space:nowrap;
                                        "
                                    >
                                        @if($assignmentItem)

                                            {{ number_format((float) $assignmentItem->discount_percent, 2) }}%

                                            <br>

                                            <span
                                                style="
                                                    color:#64748b;
                                                    font-size:8px;
                                                "
                                            >
                                                Rp {{ number_format((float) $assignmentItem->discount_amount, 2) }}
                                            </span>

                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- =================================================
                                         TAX
                                    ================================================== --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            text-align:center;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                        "
                                    >
                                        @if($assignmentItem)

                                            {{ $assignmentItem->tax_name ?? '-' }}

                                            @if((float) $assignmentItem->tax_percent > 0)
                                                <br>
                                                <span
                                                    style="
                                                        color:#64748b;
                                                        font-size:8px;
                                                    "
                                                >
                                                    {{ number_format((float) $assignmentItem->tax_percent, 2) }}%
                                                </span>
                                            @endif

                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- =================================================
                                         TAX AMOUNT
                                    ================================================== --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            text-align:right;
                                            border-right:1px solid #e2e8f0;
                                            border-bottom:1px solid #e2e8f0;
                                            white-space:nowrap;
                                        "
                                    >
                                        @if($assignmentItem)
                                            Rp {{ number_format((float) $assignmentItem->tax_amount, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- =================================================
                                         AMOUNT
                                    ================================================== --}}

                                    <td
                                        style="
                                            padding:7px 5px;
                                            text-align:right;
                                            font-weight:700;
                                            color:#12385d;
                                            border-bottom:1px solid #e2e8f0;
                                            white-space:nowrap;
                                        "
                                    >
                                        @if($assignmentItem)
                                            Rp {{ number_format((float) $assignmentItem->grand_total, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="14"
                                        style="
                                            padding:25px;
                                            text-align:center;
                                            color:#64748b;
                                        "
                                    >
                                        No Material Requisition items available.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- =========================================================
                 SIGNATURE
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
                    "
                >

                    <tr>

                        <td
                            style="
                                width:33.33%;
                                text-align:center;
                                vertical-align:top;
                                font-size:11px;
                            "
                        >
                            <strong>Requested By</strong>

                            <div
                                style="
                                    height:65px;
                                    margin:0 20px;
                                    border-bottom:1px solid #9ca3af;
                                "
                            ></div>

                            <div style="margin-top:7px;">
                                {{ $requester }}
                            </div>
                        </td>


                        <td
                            style="
                                width:33.33%;
                                text-align:center;
                                vertical-align:top;
                                font-size:11px;
                            "
                        >
                            <strong>Reviewed By</strong>

                            <div
                                style="
                                    height:65px;
                                    margin:0 20px;
                                    border-bottom:1px solid #9ca3af;
                                "
                            ></div>

                            <div style="margin-top:7px;">
                                -
                            </div>
                        </td>


                        <td
                            style="
                                width:33.33%;
                                text-align:center;
                                vertical-align:top;
                                font-size:11px;
                            "
                        >
                            <strong>Approved By</strong>

                            <div
                                style="
                                    height:65px;
                                    margin:0 20px;
                                    border-bottom:1px solid #9ca3af;
                                "
                            ></div>

                            <div style="margin-top:7px;">
                                -
                            </div>
                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>


    {{-- =========================================================
         PRINT CSS
    ========================================================== --}}
    <style>
        @media print {

            /*
            |--------------------------------------------------------------------------
            | Hide Filament application chrome
            |--------------------------------------------------------------------------
            */
            body {
                background: #fff !important;
            }

            nav,
            header,
            aside,
            [data-filament-sidebar],
            .fi-sidebar,
            .fi-topbar,
            .fi-header,
            .fi-breadcrumbs {
                display: none !important;
            }

            /*
            |--------------------------------------------------------------------------
            | Remove Preview Toolbar
            |--------------------------------------------------------------------------
            */
            #material-requisition-preview {
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            /*
            |--------------------------------------------------------------------------
            | Page Width
            |--------------------------------------------------------------------------
            */
            .fi-main {
                padding: 0 !important;
            }

            /*
            |--------------------------------------------------------------------------
            | Print Color
            |--------------------------------------------------------------------------
            */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

</x-filament-panels::page>