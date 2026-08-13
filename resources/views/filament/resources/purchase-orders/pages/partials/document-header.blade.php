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
                vertical-align:top;
                padding:0;
            ">

            <table
                style="
                    width:100%;
                    border-collapse:collapse;
                ">

                <tr>

                    <td
                        colspan="2"
                        style="
                            background:#12385d;
                            color:white;
                            text-align:center;
                            font-size:17px;
                            font-weight:bold;
                            padding:10px;
                            border-bottom:1px solid #7d93aa;
                        ">

                        PURCHASE ORDER

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            width:90px;
                            font-weight:600;
                            padding:8px;
                            border-bottom:1px solid #d7dee7;
                        ">

                        Number

                    </td>

                    <td
                        style="
                            padding:8px;
                            border-bottom:1px solid #d7dee7;
                        ">

                        {{ $purchaseOrder->document_no }}

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            font-weight:600;
                            padding:8px;
                            border-bottom:1px solid #d7dee7;
                        ">

                        Date

                    </td>

                    <td
                        style="
                            padding:8px;
                            border-bottom:1px solid #d7dee7;
                        ">

                        {{ optional($purchaseOrder->document_date)->format('d M Y') }}

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            font-weight:600;
                            padding:8px;
                        ">

                        Status

                    </td>

                    <td
                        style="
                            padding:8px;
                            font-weight:bold;
                            color:#12385d;
                        ">

                        {{ strtoupper($purchaseOrder->status ?? 'DRAFT') }}

                    </td>

                </tr>

            </table>

        </td>

    </tr>

</table>