@php
    /** @var \App\Models\PurchaseOrder $purchaseOrder */

    $supplier = $purchaseOrder->supplier;
@endphp

{{-- ===================================================== --}}
{{-- SUPPLIER & PURCHASE ORDER INFORMATION --}}
{{-- ===================================================== --}}

<table
    style="
        width:100%;
        border-collapse:collapse;
        margin-top:10px;
        table-layout:fixed;
    ">

    <tr>

        {{-- ===================================================== --}}
        {{-- SUPPLIER INFORMATION --}}
        {{-- ===================================================== --}}

        <td
            style="
                width:55%;
                vertical-align:top;
                padding-right:8px;
            ">

            <table
                style="
                    width:100%;
                    border-collapse:collapse;
                    border:1px solid #7d93aa;
                ">

                <tr>

                    <td
                        colspan="2"
                        style="
                            background:#12385d;
                            color:#ffffff;
                            font-weight:700;
                            font-size:12px;
                            padding:7px 10px;
                            letter-spacing:.2px;
                            letter-spacing:.3px;
                        ">

                        SUPPLIER INFORMATION

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            width:110px;
                            padding:5px 8px;
                            font-size:11px;
                            font-weight:600;
                            background:#fafbfd;
                        ">

                        Vendor

                    </td>

                    <td style="padding:5px 8px;
                                font-size:11px;
                                line-height:16px;">

                        @if($supplier)

                        <div
                            style="
                                font-size:11px;
                                font-weight:700;
                                color:#12385d;
                                margin-bottom:1px;
                            ">

                            {{ $supplier->supplier_code }}

                        </div>

                        <div
                            style="
                                font-size:11px;
                                line-height:15px;
                            ">

                            {{ $supplier->supplier_name }}

                        </div>

                        @else

                            -

                        @endif

                    </td>

                </tr>


                <tr>

                    <td
                        style="
                            padding:8px 10px;
                            font-weight:600;
                            background:#fafbfd;
                        ">

                        Phone

                    </td>

                    <td style="padding:8px 10px;">

                        {{ $supplier?->phone ?? '-' }}

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            padding:8px 10px;
                            font-weight:600;
                            background:#fafbfd;
                        ">

                        Email

                    </td>

                    <td style="padding:8px 10px;">

                        {{ $supplier?->email ?? '-' }}

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            padding:8px 10px;
                            font-weight:600;
                            vertical-align:top;
                            background:#fafbfd;
                        ">

                        Address

                    </td>

                    <td
                        style="
                            padding:8px 10px;
                            line-height:18px;
                        ">

                        {!! nl2br(e($supplier?->address ?? '-')) !!}

                    </td>

                </tr>

            </table>

        </td>

        {{-- ===================================================== --}}
        {{-- PURCHASE ORDER INFORMATION --}}
        {{-- ===================================================== --}}

        <td
            style="
                width:45%;
                vertical-align:top;
            ">

            <table
                style="
                    width:100%;
                    border-collapse:collapse;
                    border:1px solid #7d93aa;
                ">

                <tr>

                    <td
                        colspan="2"
                        style="
                            background:#12385d;
                            color:#ffffff;
                            font-weight:700;
                            font-size:13px;
                            padding:9px 12px;
                            letter-spacing:.3px;
                        ">

                        PURCHASE ORDER INFORMATION

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            width:120px;
                            padding:5px 8px;
                            font-size:11px;
                            font-weight:600;
                            background:#fafbfd;
                        ">

                        Document Date

                    </td>

                    <td style="padding:5px 8px;
                                font-size:11px;
                                line-height:16px;">

                        {{ optional($purchaseOrder->document_date)->format('d M Y') }}

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            padding:8px 10px;
                            font-weight:600;
                            background:#fafbfd;
                        ">

                        Delivery Date

                    </td>

                    <td style="padding:5px 8px;
                            font-size:11px;
                            line-height:16px;">

                        {{ optional($purchaseOrder->expected_delivery_date)->format('d M Y') }}

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            padding:5px 8px;
                            font-size:11px;
                            line-height:16px;
                            font-weight:600;
                            background:#fafbfd;
                            white-space:nowrap;
                        ">

                        T.O.P

                    </td>

                    <td
                        style="
                            padding:5px 8px;
                            font-size:11px;
                            line-height:16px;
                        ">

                        {{ $purchaseOrder->paymentTerm?->term_name ?? '-' }}

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            padding:5px 8px;
                            font-size:11px;
                            line-height:16px;
                            font-weight:600;
                            background:#fafbfd;
                        ">

                        Payment

                    </td>

                    <td style="padding:5px 8px;
                                font-size:11px;
                                line-height:16px;">

                        {{ $purchaseOrder->payment_method ?? 'BANK TRANSFER' }}

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            padding:5px 8px;
                            font-size:11px;
                            line-height:16px;
                            font-weight:600;
                            background:#fafbfd;
                        ">

                        Delivery Time

                    </td>

                    <td style="padding:5px 8px;
                                font-size:11px;
                                line-height:16px;">

                        {{ $purchaseOrder->delivery_time ?? '14 DAYS AFTER PO' }}

                    </td>

                </tr>

            </table>

        </td>

    </tr>

</table>



{{-- ===================================================== --}}
{{-- SHIPPING TO (MASTER SHIPPING ADDRESS) --}}
{{-- ===================================================== --}}

@php

    $shipping = $purchaseOrder->shippingAddress;

@endphp

<table
    style="
        width:100%;
        border-collapse:collapse;
        margin-top:10px;
        border:1px solid #7d93aa;
    ">

    <tr>

        <td
            colspan="2"
            style="
                background:#12385d;
                color:#ffffff;
                font-weight:700;
                padding:9px 12px;
                font-size:13px;
            ">

            SHIPPING TO

        </td>

    </tr>

    <tr>

        {{-- ========================================== --}}
        {{-- ADDRESS --}}
        {{-- ========================================== --}}

        <td
            style="
                width:65%;
                padding:8px 10px;
                vertical-align:top;
                line-height:21px;
                font-size:13px;
            ">

            @if($shipping)

                <div
                    style="
                        font-weight:700;
                        font-size:15px;
                        color:#12385d;
                        margin-bottom:8px;
                    ">

                    {{ $shipping->shipping_name }}

                </div>

                {{ $shipping->address }}<br>

                @if($shipping->city)

                    {{ $shipping->city }}

                @endif

                @if($shipping->province)

                    , {{ $shipping->province }}

                @endif

                @if($shipping->postal_code)

                    {{ $shipping->postal_code }}

                @endif

                <br>

                {{ $shipping->country }}

            @else

                -

            @endif

        </td>

        {{-- ========================================== --}}
        {{-- CONTACT --}}
        {{-- ========================================== --}}

        <td
            style="
                width:35%;
                padding:8px 10px;
                vertical-align:top;
                font-size:13px;
                line-height:18px;
            ">

            <table
                style="
                    width:100%;
                    border:none;
                    border-collapse:collapse;
                ">

                <tr>

                    <td
                        style="
                            width:120px;
                            border:none;
                            padding:1px 0;
                            font-weight:600;
                        ">

                        Attention :

                    </td>

                    <td
                        style="
                            border:none;
                            padding:2px 0;
                        ">

                        {{ $shipping?->attention ?? '-' }}

                    </td>

                </tr>


                <tr>

                    <td
                        style="
                            border:none;
                            padding:2px 0;
                            font-weight:600;
                        ">

                        Phone (WA) :

                    </td>

                    <td
                        style="
                            border:none;
                            padding:2px 0;
                        ">

                        {{ $shipping?->phone ?? '-' }}

                    </td>

                </tr>

                <tr>

                    <td
                        style="
                            border:none;
                            padding:2px 0;
                            font-weight:600;
                        ">

                        Email :  

                    </td>

                    <td
                        style="
                            border:none;
                            padding:2px 0;
                        ">

                        {{ $shipping?->email ?? '-' }}

                    </td>

                </tr>

            </table>

        </td>

    </tr>

</table>