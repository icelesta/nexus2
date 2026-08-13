{{-- ===========================================================
| ERP DOCUMENT HEADER
| Nexus 2.0 ERP
=========================================================== --}}

@php

$company = [
    'name'      => 'PT BESMINDO MATERI SEWATAMA',
    'tagline'   => 'Oilfield Equipment Sales & Rental, Drilling & Work Over Rig Services',
    'location'  => 'Light Vehicle & Logistics Yard Duri',
];

@endphp

<table
    style="
        width:100%;
        border-collapse:collapse;
        border:1px solid #CBD5E1;
        background:#FFFFFF;
        table-layout:fixed;
    ">

    <tr style="height:108px;">

        {{-- ===================================================== --}}
        {{-- LOGO --}}
        {{-- ===================================================== --}}

        <td
            style="
                width:110px;
                text-align:center;
                vertical-align:middle;
                border-right:1px solid #CBD5E1;
            ">

            <img
                src="{{ asset('images/company/icon.png') }}"
                style="
                    width:72px;
                    height:auto;
                ">

        </td>

        {{-- ===================================================== --}}
        {{-- COMPANY --}}
        {{-- ===================================================== --}}

        <td
            style="
                padding:18px;
                vertical-align:middle;
            ">

            <div
                style="
                    font-size:26px;
                    font-weight:700;
                    color:#163A63;
                    letter-spacing:.3px;
                ">

                {{ $company['name'] }}

            </div>

            <div
                style="
                    margin-top:10px;
                    font-size:11px;
                    color:#444;
                ">

                {{ $company['tagline'] }}

            </div>

            <div
                style="
                    margin-top:4px;
                    font-size:11px;
                    color:#444;
                ">

                {{ $company['location'] }}

            </div>

        </td>

        {{-- ===================================================== --}}
        {{-- DOCUMENT INFO --}}
        {{-- ===================================================== --}}

        <td
            style="
                width:250px;
                padding:0;
                border-left:1px solid #CBD5E1;
                vertical-align:top;
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
                            background:#183B63;
                            color:white;
                            font-size:20px;
                            font-weight:700;
                            text-align:center;
                            padding:12px;
                        ">

                        MATERIAL REQUISITION

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            width:90px;
                            padding:8px 10px;
                            border:1px solid #CBD5E1;
                            font-size:12px;
                            font-weight:600;
                            background:#FAFAFA;
                        ">

                        Number

                    </td>

                    <td
                        style="
                            padding:8px 10px;
                            border:1px solid #CBD5E1;
                            font-size:12px;
                        ">

                        {{ $record->document_no }}

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            padding:8px 10px;
                            border:1px solid #CBD5E1;
                            font-size:12px;
                            font-weight:600;
                            background:#FAFAFA;
                        ">

                        Date

                    </td>

                    <td
                        style="
                            padding:8px 10px;
                            border:1px solid #CBD5E1;
                            font-size:12px;
                        ">

                        {{ optional($record->document_date)->format('d M Y') }}

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            padding:8px 10px;
                            border:1px solid #CBD5E1;
                            font-size:12px;
                            font-weight:600;
                            background:#FAFAFA;
                        ">

                        Status

                    </td>

                    <td
                        style="
                            padding:8px 10px;
                            border:1px solid #CBD5E1;
                            font-size:13px;
                            font-weight:700;
                            color:#1E40AF;
                        ">

                        {{ strtoupper($record->status) }}

                    </td>

                </tr>

            </table>

        </td>

    </tr>

</table>