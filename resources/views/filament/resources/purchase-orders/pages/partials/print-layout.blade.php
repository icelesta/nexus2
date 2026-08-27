<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Purchase Order - {{ $purchaseOrder->document_no }}
    </title>

    <style>

        /* ==========================================================
           NEXUS ERP 2.0
           PURCHASE ORDER PRINT / PDF MASTER LAYOUT
           A4 PORTRAIT - COMPACT CORPORATE
           ========================================================== */

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }


        /* ==========================================================
           PAGE
           ========================================================== */

        @page {
            size: A4 portrait;
            margin: 6mm;
        }


        html {
            background: #e9edf3;
            min-height: 100%;
        }


        body {
            margin: 0;
            padding: 14px 0;
            background: #e9edf3;
            color: #1b1b1b;

            font-family:
                "Segoe UI",
                Arial,
                Helvetica,
                sans-serif;

            font-size: 8.5pt;
            line-height: 1.22;
        }


        /* ==========================================================
           DOCUMENT
           ========================================================== */

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
            padding: 6mm;

            background: #ffffff;
            border: 1px solid #d9e2ec;

            box-shadow:
                0 3px 10px rgba(0, 0, 0, .08),
                0 15px 35px rgba(0, 0, 0, .12);
        }


        /* ==========================================================
           TYPOGRAPHY
           ========================================================== */

        h1 {
            font-size: 18px;
            font-weight: 700;
            color: #12385d;
        }

        h2 {
            font-size: 14px;
            font-weight: 700;
        }

        h3 {
            font-size: 11px;
            font-weight: 700;
        }

        p {
            margin: 1px 0;
        }

        small {
            color: #777;
        }


        /* ==========================================================
           SECTION SPACING
           ========================================================== */

        .section {
            width: 100%;
            margin-top: 2.2mm;
        }

        .section:first-child {
            margin-top: 0;
        }

        .section:last-child {
            margin-bottom: 0;
        }


        /* ==========================================================
           GLOBAL TABLE
           ========================================================== */

        table {
            width: 100%;
            max-width: 100%;

            border-collapse: collapse;
            border-spacing: 0;

            page-break-inside: auto;
        }


        th {
            padding: 3px 4px;

            background: #eef4fa;
            color: #12385d;

            font-size: 8pt;
            font-weight: 700;

            border: 1px solid #97a8bb;
        }


        td {
            padding: 3px 4px;

            vertical-align: top;

            border: 1px solid #b8c3cf;

            font-size: 8pt;
            line-height: 1.2;
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


        /* ==========================================================
           ALIGNMENT
           ========================================================== */

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-left {
            text-align: left !important;
        }

        .fw-bold {
            font-weight: 700;
        }


        /* ==========================================================
           UTILITY
           ========================================================== */

        .mb-5 {
            margin-bottom: 3mm;
        }

        .mb-8 {
            margin-bottom: 5mm;
        }

        .mt-8 {
            margin-top: 5mm;
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


        /* ==========================================================
           DOCUMENT BLOCKS
           ========================================================== */

        .document-header,
        .po-header,
        .po-summary,
        .po-signature {
            page-break-inside: avoid;
        }


        /* ==========================================================
           COMPACT HEADER
           ========================================================== */

        .document-header table,
        .po-header table {
            margin: 0 !important;
        }


        .document-header th,
        .document-header td,
        .po-header th,
        .po-header td {
            padding: 3px 4px !important;
            font-size: 8pt !important;
            line-height: 1.18 !important;
        }


        /* ==========================================================
           COMPACT ITEM TABLE
           ========================================================== */

        .po-items table {
            margin: 0 !important;
        }


        .po-items th {
            padding: 4px 4px !important;
            font-size: 8pt !important;
            line-height: 1.15 !important;
        }


        .po-items td {
            padding: 3px 4px !important;
            font-size: 8pt !important;
            line-height: 1.18 !important;
        }


        /* Item code */

        .po-items .item-code {
            font-size: 7pt !important;
            line-height: 1.1 !important;
        }


        /* Item name */

        .po-items .item-name {
            font-size: 8pt !important;
            line-height: 1.15 !important;
            font-weight: 700 !important;
        }


        /* Item specification */

        .po-items .item-spec {
            font-size: 7pt !important;
            line-height: 1.1 !important;
        }


        /* Total item row */

        .po-items .total-item,
        .po-items .total-items {
            padding: 3px 4px !important;
            font-size: 7.5pt !important;
            line-height: 1.1 !important;
        }


        /* ==========================================================
           COMPACT SUMMARY
           ========================================================== */

        .po-summary {
            margin-top: 2.2mm !important;
        }


        .po-summary-layout {
            width: 100% !important;
            margin: 0 !important;
        }


        .po-summary-layout > tbody > tr > td {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }


        .po-remark-cell {
            width: 50% !important;
            padding-right: 4px !important;
        }


        .po-remark-box {
            padding: 6px 8px !important;

            font-size: 8pt !important;
            line-height: 1.18 !important;

            text-align: left !important;
        }


        .po-remark-title {
            margin-bottom: 5px !important;

            font-size: 9pt !important;
            line-height: 1.1 !important;

            text-align: left !important;
        }


        .po-remark-content {
            display: block !important;

            width: 100% !important;

            margin: 0 !important;
            padding: 0 !important;

            float: none !important;

            font-size: 8pt !important;
            line-height: 1.18 !important;

            text-align: left !important;

            white-space: pre-wrap;
            word-break: break-word;
        }


        .po-summary-cell {
            width: 50% !important;
            padding-left: 4px !important;
        }


        .po-summary-table {
            width: 100% !important;
            max-width: none !important;

            margin: 0 !important;
        }


        .po-summary-table td {
            padding: 3px 5px !important;

            font-size: 7.8pt !important;
            line-height: 1.15 !important;
        }


        .summary-label {
            font-weight: 600;
        }


        .summary-value {
            text-align: right !important;
            font-weight: 500;
        }


        .summary-grand {
            font-size: 8.5pt !important;
            font-weight: 700 !important;
        }


        /* ==========================================================
           COMPACT SIGNATURE
           ========================================================== */

        .po-signature {
            margin-top: 2.5mm !important;
        }


        .signature-table {
            width: 100% !important;

            margin-top: 0 !important;

            border-collapse: collapse !important;
        }


        .signature-table td {
            width: 50% !important;

            padding: 5px 8px 7px !important;

            height: 100px !important;

            font-size: 8pt !important;
            line-height: 1.15 !important;
        }


        .signature-title {
            margin-bottom: 30px !important;

            font-size: 8.5pt !important;
            line-height: 1.1 !important;
        }


        .signature-line {
            width: 82% !important;

            padding-top: 4px !important;
        }


        .signature-position {
            font-size: 7pt !important;

            margin-top: 1px !important;
        }


        .signature-date {
            font-size: 7pt !important;

            margin-top: 2px !important;
        }


        .signature-name {
            font-size: 8pt !important;

            font-weight: 600 !important;
        }


        /* ==========================================================
           COMPACT FOOTER
           ========================================================== */

        .po-footer {
            display: block;

            width: 100%;

            margin-top: 2.5mm !important;
        }


        .document-footer {
            margin-top: 10px !important;

            padding-top: 5px !important;

            font-size: 7pt !important;

            line-height: 1.15 !important;
        }


        .document-footer table {
            width: 100% !important;

            margin: 0 !important;
        }


        .document-footer td {
            padding: 2px 0 !important;

            font-size: 7pt !important;
            line-height: 1.1 !important;
        }


        .footer-right {
            padding-right: 8px !important;
        }


        .footer-note {
            margin-top: 4px !important;

            font-size: 6.5pt !important;
            line-height: 1.15 !important;
        }


        /* ==========================================================
           IMAGES
           ========================================================== */

        <img
            src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/company/logo-besmindo.png'))) }}"
            alt="Besmindo Logo"
        >


        /* ==========================================================
           SCREEN
           ========================================================== */

        @media screen {

            html {
                background: #e9edf3;
            }


            body {
                background: #e9edf3;

                padding: 14px 0;

                font-size: 8.5pt;
            }


            .document-wrapper {
                width: 100%;

                padding: 0;
            }


            .document {
                width: 190mm;

                min-height: 277mm;

                margin: 0 auto;

                padding: 6mm;

                background: #ffffff;

                border: 1px solid #d9e2ec;

                box-shadow:
                    0 6px 20px rgba(0, 0, 0, .10);
            }

        }


        /* ==========================================================
           PRINT
           ========================================================== */

        @media print {

            html {
                background: #ffffff;
            }


            body {
                margin: 0;
                padding: 0;

                background: #ffffff;

                font-size: 8.5pt;

                line-height: 1.22;
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

                margin-top: 2.2mm;
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


            .po-items {
                page-break-inside: auto !important;
            }


            .po-items tr {
                page-break-inside: avoid !important;
            }

        }

    </style>

</head>


<body>

<div class="document-wrapper">

    <div class="document">

        {{-- =====================================================
        CORPORATE HEADER
        ====================================================== --}}

        <section class="section document-header avoid-break">

            @include(
                'filament.resources.purchase-orders.pages.partials.document-header'
            )

        </section>


        {{-- =====================================================
        PURCHASE ORDER HEADER
        ====================================================== --}}

        <section class="section po-header avoid-break">

            @include(
                'filament.resources.purchase-orders.pages.partials.po-header'
            )

        </section>


        {{-- =====================================================
        ITEM TABLE
        ====================================================== --}}

        <section class="section po-items">

            @include(
                'filament.resources.purchase-orders.pages.partials.po-items-table'
            )

        </section>


        {{-- =====================================================
        SUMMARY
        ====================================================== --}}

        <section class="section po-summary avoid-break">

            @include(
                'filament.resources.purchase-orders.pages.partials.po-summary'
            )

        </section>


        {{-- =====================================================
        SIGNATURE
        ====================================================== --}}

        <section class="section po-signature avoid-break">

            @include(
                'filament.resources.purchase-orders.pages.partials.po-signature'
            )

        </section>


        {{-- =====================================================
        FOOTER
        ====================================================== --}}

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