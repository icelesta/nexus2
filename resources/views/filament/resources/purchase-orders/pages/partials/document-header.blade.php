@php
    /** @var \App\Models\PurchaseOrder|null $purchaseOrder */

    $purchaseOrder = $purchaseOrder ?? $record ?? null;

    $company = [
        'name' => 'PT BESMINDO MATERI SEWATAMA',
        'tagline' => 'Oilfield Equipment Sales & Rental, Drilling & Work Over Rig Services',
        'service_1' => '',
        'service_2' => 'Light Vehicle & Logistics Yard Duri',
    ];
@endphp

<div class="po-document-header">

    <table style="width:100%; border-collapse:collapse; border:1px solid #7d93aa;">

    <tr>

        {{-- ===================================================== --}}
        {{-- COMPANY LOGO --}}
        {{-- ===================================================== --}}

        <td
            style="
                width:110px;
                text-align:center;
                vertical-align:middle;
                border-right:1px solid #7d93aa;
                padding:12px;
            ">

            <img
                src="{{ asset('images/company/icon.png') }}"
                alt="Besmindo Logo"
                style="
                    width:90px;
                    height:auto;
                ">

        </td>

        {{-- ===================================================== --}}
        {{-- COMPANY INFORMATION --}}
        {{-- ===================================================== --}}

        <td
            style="
                padding:14px 18px;
                vertical-align:middle;
            ">

            <div
                style="
                    font-size:18px;
                    font-weight:700;
                    color:#12385d;
                    letter-spacing:.5px;
                ">

                {{ $company['name'] }}

            </div>

            <div
                style="
                    margin-top:8px;
                    font-size:10px;
                    color:#333;
                ">

                {{ $company['tagline'] }}

            </div>

            <div
                style="
                    margin-top:3px;
                    font-size:10px;
                    color:#333;
                ">

                {{ $company['service_1'] }}

            </div>

            <div
                style="
                    margin-top:3px;
                    font-size:10px;
                    color:#333;
                ">

                {{ $company['service_2'] }}

            </div>

        </td>

        
        {{-- ===================================================== --}}
        {{-- DOCUMENT INFO --}}
        {{-- ===================================================== --}}

        <td
            style="
                width:230px;
                border-left:1px solid #7d93aa;
                border-right:1px solid #7d93aa;
                border-top:1px solid #7d93aa;
                border-bottom:1px solid #7d93aa;
                vertical-align:top;
                padding:0;
            "
        >

            <table
                style="
                    width:100%;
                    border:none;
                    border-collapse:collapse;
                    border-spacing:0;
                "
            >

                {{-- =============================================
                PURCHASE ORDER TITLE
                ============================================= --}}

                <tr>

                    <td
                        colspan="2"
                        style="
                            background:#12385d;
                            color:#ffffff;
                            text-align:center;
                            font-size:17px;
                            font-weight:bold;
                            padding:10px;
                            border:none;
                            border-bottom:1px solid #7d93aa;
                        "
                    >

                        PURCHASE ORDER

                    </td>

                </tr>


                {{-- =============================================
                NUMBER
                ============================================= --}}

                <tr>

                    <td
                        style="
                            width:95px;
                            font-weight:600;
                            padding:8px;
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
                            padding:8px;
                            border:none;
                            border-bottom:1px solid #d7dee7;
                            vertical-align:middle;
                            white-space:nowrap;
                        "
                    >

                        {{ $purchaseOrder->document_no }}

                    </td>

                </tr>


                {{-- =============================================
                APPROVAL STATUS
                ============================================= --}}

                <tr>

                    <td
                        style="
                            width:95px;
                            font-weight:600;
                            padding:8px;
                            border:none;
                            vertical-align:middle;
                            white-space:nowrap;
                        "
                    >

                        Approval Status

                    </td>

                    <td
                        style="
                            padding:8px;
                            border:none;
                            vertical-align:middle;
                            font-weight:bold;
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
    .fi-page.fi-resource-purchase-order-resource-purchase-orders
    .fi-grid-col:has(.po-document-header) {
        position: sticky;
        top: 0;
        align-self: start;
        z-index: 40;
    }

    .po-document-header {
        position: relative;
        z-index: 41;
        background: white;
    }
</style>