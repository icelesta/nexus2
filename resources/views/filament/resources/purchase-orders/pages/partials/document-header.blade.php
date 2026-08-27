@php
    /** @var \App\Models\PurchaseOrder|null $purchaseOrder */

    $purchaseOrder = $purchaseOrder ?? $record ?? null;

    /*
    |--------------------------------------------------------------------------
    | COMPANY INFORMATION
    |--------------------------------------------------------------------------
    */

    $company = [
        'name'     => 'PT BESMINDO MATERI SEWATAMA',
        'tagline'  => 'Oilfield Equipment Sales & Rental, Drilling & Work Over Rig Services',
        'service_1' => '',
        'service_2' => 'Light Vehicle & Logistics Yard Duri',
    ];


    /*
    |--------------------------------------------------------------------------
    | COMPANY LOGO
    |--------------------------------------------------------------------------
    |
    | Keep Base64.
    |
    | This is important because the same header must work in:
    |
    | - Browser Preview
    | - Browser Print
    | - DomPDF Export
    |
    */

    $logoPath = public_path(
        'images/company/icon.png'
    );

    $logoData = null;

    if (is_file($logoPath)) {

        $logoData =
            'data:image/png;base64,' .
            base64_encode(
                file_get_contents($logoPath)
            );

    }

@endphp


<div class="po-document-header">

    <table
        style="
            width:100%;
            border-collapse:collapse;
            border:1px solid #7d93aa;
            table-layout:fixed;
        "
    >

        <tr>

            {{-- =================================================
                 COMPANY LOGO
                 ================================================= --}}

                <td
                    style="
                        width:20%;
                        padding:6px 10px;
                        text-align:center;
                        vertical-align:middle;
                        border-right:1px solid #7d93aa;
                    "
                >

                @if($logoData)

                    <img
                        src="{{ $logoData }}"
                        alt="Besmindo Logo"
                        style="
                            display:block;
                            width:78px;
                            max-width:100%;
                            height:auto;
                            margin:0 auto;
                        "
                    >

                @else

                    <div
                        style="
                            font-size:9px;
                            color:#9ca3af;
                            text-align:center;
                        "
                    >
                        BESMINDO
                    </div>

                @endif

            </td>


            {{-- =================================================
                 COMPANY INFORMATION
                 ================================================= --}}

            <td
                style="
                    width:50%;
                    padding:7px 14px;
                    vertical-align:middle;
                "
            >

                <div
                    style="
                        font-size:15px;
                        font-weight:700;
                        color:#12385d;
                        letter-spacing:.2px;
                        line-height:18px;
                        white-space:nowrap;
                    "
                >

                    {{ $company['name'] }}

                </div>


                @if(filled($company['tagline']))

                    <div
                        style="
                            margin-top:4px;
                            font-size:9px;
                            color:#333333;
                            line-height:12px;
                        "
                    >

                        {{ $company['tagline'] }}

                    </div>

                @endif


                @if(filled($company['service_1']))

                    <div
                        style="
                            margin-top:1px;
                            font-size:9px;
                            color:#333333;
                            line-height:12px;
                        "
                    >

                        {{ $company['service_1'] }}

                    </div>

                @endif


                @if(filled($company['service_2']))

                    <div
                        style="
                            margin-top:1px;
                            font-size:9px;
                            color:#333333;
                            line-height:12px;
                        "
                    >

                        {{ $company['service_2'] }}

                    </div>

                @endif

            </td>


            {{-- =================================================
                 PURCHASE ORDER INFORMATION
                 ================================================= --}}

            <td
                style="
                    width:30%;
                    padding:0;
                    vertical-align:top;
                    border-left:1px solid #7d93aa;
                "
            >

                    <table
                        style="
                            width:100%;
                            border:none;
                            border-collapse:collapse;
                            border-spacing:0;
                            table-layout:fixed;
                        "
                    >

                    {{-- =========================================
                         PURCHASE ORDER TITLE
                         ========================================= --}}

                    <tr>

                        <td
                            colspan="2"
                            style="
                                background:#12385d;
                                color:#ffffff;
                                text-align:center;
                                font-size:15px;
                                font-weight:700;
                                padding:6px 4px;
                                border:none;
                                border-bottom:1px solid #7d93aa;
                                line-height:17px;
                                white-space:nowrap;
                            "
                        >

                            PURCHASE ORDER

                        </td>

                    </tr>


                    {{-- =========================================
                         NUMBER
                         ========================================= --}}

                    <tr>

                        <td
                            style="
                                width:90px;
                                font-weight:600;
                                font-size:9px;
                                padding:5px 7px;
                                border:none;
                                border-bottom:1px solid #d7dee7;
                                vertical-align:middle;
                                white-space:nowrap;
                            "
                        >

                            Number

                        </td>

                        <td
                            style="
                                padding:5px 7px;
                                font-size:9px;
                                border:none;
                                border-bottom:1px solid #d7dee7;
                                vertical-align:middle;
                                white-space:nowrap;
                            "
                        >

                            {{ $purchaseOrder->document_no }}

                        </td>

                    </tr>


                    {{-- =========================================
                         APPROVAL STATUS
                         ========================================= --}}

                    <tr>

                        <td
                            style="
                                width:90px;
                                font-weight:600;
                                font-size:9px;
                                padding:5px 7px;
                                border:none;
                                vertical-align:middle;
                                white-space:nowrap;
                            "
                        >

                            Approval Status

                        </td>

                        <td
                            style="
                                padding:5px 7px;
                                font-size:10px;
                                border:none;
                                vertical-align:middle;
                                font-weight:700;
                                color:#12385d;
                                white-space:nowrap;
                            "
                        >

                            {{ strtoupper($purchaseOrder->approval_status ?? 'PENDING') }}

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>

</div>


<style>

    /*
    |--------------------------------------------------------------------------
    | Purchase Order Header
    |--------------------------------------------------------------------------
    |
    | Keep header stable in Preview and Print.
    |
    */

    .po-document-header {

        position: relative;

        z-index: 41;

        width: 100%;

        background: #ffffff;

    }


    /*
    |--------------------------------------------------------------------------
    | Filament Preview Sticky Header
    |--------------------------------------------------------------------------
    */

    .fi-page.fi-resource-purchase-order-resource-purchase-orders
    .fi-grid-col:has(.po-document-header) {

        position: sticky;

        top: 0;

        align-self: start;

        z-index: 40;

    }


    /*
    |--------------------------------------------------------------------------
    | Screen
    |--------------------------------------------------------------------------
    */

    @media screen {

        .po-document-header {

            width: 100%;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Print / DomPDF
    |--------------------------------------------------------------------------
    */

    @media print {

        .po-document-header {

            width: 100%;

            margin: 0;

            padding: 0;

        }

    }

</style>