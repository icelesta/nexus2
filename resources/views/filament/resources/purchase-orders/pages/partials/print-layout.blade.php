<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Purchase Order - {{ $purchaseOrder->document_no }}
    </title>

    <style>

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            background: #e9edf3;
            min-height: 100%;
        }

        body {
            margin: 0;
            padding: 24px 0;
            background: #e9edf3;
            color: #1b1b1b;
            font-family:
                "Segoe UI",
                Arial,
                Helvetica,
                sans-serif;
            font-size: 10pt;
            line-height: 1.35;
        }

        .document-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 0;
        }

        .document {
            width: 190mm;
            min-height: 277mm;
            margin: 0 auto;
            padding: 7mm;
            background: #ffffff;
            border: 1px solid #d9e2ec;

            box-shadow:
                0 3px 10px rgba(0, 0, 0, .08),
                0 15px 35px rgba(0, 0, 0, .12);
        }

        h1 {
            font-size: 22px;
            font-weight: 700;
            color: #12385d;
        }

        h2 {
            font-size: 17px;
            font-weight: 700;
        }

        h3 {
            font-size: 14px;
            font-weight: 700;
        }

        p {
            margin: 2px 0;
        }

        small {
            color: #777;
        }

        .section {
            width: 100%;
            margin-top: 4mm;
        }

        .section:first-child {
            margin-top: 0;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            page-break-inside: auto;
        }

        th {
            padding: 6px;
            background: #eef4fa;
            color: #12385d;
            font-size: 10pt;
            font-weight: 700;
            border: 1px solid #97a8bb;
        }

        td {
            padding: 6px;
            vertical-align: top;
            border: 1px solid #b8c3cf;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .fw-bold {
            font-weight: 700;
        }

        .mb-5 {
            margin-bottom: 5mm;
        }

        .mb-8 {
            margin-bottom: 8mm;
        }

        .mt-8 {
            margin-top: 8mm;
        }

        .border-top {
            border-top: 1px solid #bfc7cf;
        }

        .page-break {
            page-break-after: always;
        }

        .avoid-break {
            page-break-inside: avoid;
        }

        .document-header,
        .po-header,
        .po-summary,
        .po-signature {
            page-break-inside: avoid;
        }

        /*
        ================================================
        FOOTER
        ================================================
        Footer dikembalikan.
        Jangan hide .po-footer.
        */

        .po-footer {
            display: block;
            width: 100%;
        }

        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        @media screen {

            html {
                background: #e9edf3;
            }

            body {
                background: #e9edf3;
                padding: 20px 0;
            }

            .document-wrapper {
                width: 100%;
                padding: 0;
            }

            .document {
                width: 190mm;
                min-height: 277mm;
                margin: 0 auto;
                padding: 7mm;
                background: #ffffff;
                border: 1px solid #d9e2ec;

                box-shadow:
                    0 6px 20px rgba(0, 0, 0, .10);
            }

        }

        @media print {

            html {
                background: #ffffff;
            }

            body {
                margin: 0;
                padding: 0;
                background: #ffffff;
                font-size: 9.5pt;
                line-height: 1.30;
            }

            .document-wrapper {
                display: block;
                width: 100%;
                margin: 0;
                padding: 0;
                background: #ffffff;
            }

            .document {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                background: #ffffff;
                border: none;
                box-shadow: none;
            }

            .section {
                width: 100%;
                margin-top: 3mm;
            }

            .section:first-child {
                margin-top: 0;
            }

            table {
                width: 100% !important;
                max-width: 100% !important;
            }

            img {
                max-width: 100% !important;
                height: auto !important;
            }

            hr {
                display: none !important;
            }

            .po-footer {
                display: block !important;
            }

            .po-summary,
            .po-signature {
                page-break-inside: avoid !important;
            }

        }

    </style>

</head>


<body>

<div class="document-wrapper">

    <div class="document">

        {{-- =============================================
        Corporate Header
        ============================================= --}}

        <section class="section document-header avoid-break">

            @include(
                'filament.resources.purchase-orders.pages.partials.document-header'
            )

        </section>


        {{-- =============================================
        Purchase Order Header
        ============================================= --}}

        <section class="section po-header avoid-break">

            @include(
                'filament.resources.purchase-orders.pages.partials.po-header'
            )

        </section>


        {{-- =============================================
        Item Table
        ============================================= --}}

        <section class="section po-items">

            @include(
                'filament.resources.purchase-orders.pages.partials.po-items-table'
            )

        </section>


        {{-- =============================================
        Summary
        ============================================= --}}

        <section class="section po-summary avoid-break">

            @include(
                'filament.resources.purchase-orders.pages.partials.po-summary'
            )

        </section>


        {{-- =============================================
        Signature
        ============================================= --}}

        <section class="section po-signature avoid-break">

            @include(
                'filament.resources.purchase-orders.pages.partials.po-signature'
            )

        </section>


        {{-- =============================================
        Footer
        ============================================= --}}

        <section class="section po-footer">

            @include(
                'filament.resources.purchase-orders.pages.partials.po-footer'
            )

        </section>

    </div>

</div>


@if($autoPrint)

<script>

window.addEventListener('load', function () {
    window.print();
});

</script>

@endif


</body>

</html>
