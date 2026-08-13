{{-- ==========================================================
| Nexus ERP 2.0
| Purchase Order Print Layout
| Corporate Edition V3.1
| Sprint PO-Print 3.1
========================================================== --}}

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Purchase Order - {{ $purchaseOrder->document_no }}
    </title>

    <style>

        /*************************************************
        RESET
        *************************************************/

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        html{

            background:#e9edf3;

            min-height:100%;

        }

        body{

            font-family:"Segoe UI",Arial,Helvetica,sans-serif;

            color:#1b1b1b;

            background:#e9edf3;

            font-size:10pt;

            line-height:1.35;

            margin:0;

            padding:30px 0;

        }

        /*************************************************
        PAGE
        *************************************************/

        .document-wrapper{

            width:100%;

            display:flex;

            justify-content:center;

            align-items:flex-start;

            padding:24px 0;

            background:#edf1f7;

        }        

        .document{

            width:190mm;

            min-height:277mm;

            background:#ffffff;

            padding:8mm;

            border:1px solid #d9e2ec;

            box-shadow:
                0 3px 10px rgba(0,0,0,.08),
                0 15px 35px rgba(0,0,0,.12);

        }

        /*************************************************
        TYPOGRAPHY
        *************************************************/

        h1{

            font-size:22px;

            font-weight:700;

            color:#12385d;

        }

        h2{

            font-size:17px;

            font-weight:700;

        }

        h3{

            font-size:14px;

            font-weight:700;

        }

        p{

            margin:2px 0;

        }

        small{

            color:#777;

        }

        /*************************************************
        SECTION
        *************************************************/

        .section{

            margin-top:5mm;

        }

        .section:first-child{

            margin-top:0;

        }

        /*************************************************
        TABLE
        *************************************************/

        table{

            width:100%;

            border-collapse:collapse;

        }

        th{

            background:#eef4fa;

            color:#12385d;

            font-size:10pt;

            font-weight:700;

            border:1px solid #97a8bb;

            padding:6px;

        }

        td{

            border:1px solid #b8c3cf;

            padding:6px;

            vertical-align:top;

        }

        table{

            width:100%;

            border-collapse:collapse;

            page-break-inside:auto;

        }

        tr{

            page-break-inside:avoid;

            page-break-after:auto;

        }

        thead{

            display:table-header-group;

        }

        tfoot{

            display:table-footer-group;

        }


        /*************************************************
        HELPERS
        *************************************************/

        .text-center{

            text-align:center;

        }

        .text-right{

            text-align:right;

        }

        .text-left{

            text-align:left;

        }

        .fw-bold{

            font-weight:700;

        }

        .mb-5{

            margin-bottom:5mm;

        }

        .mb-8{

            margin-bottom:8mm;

        }

        .mt-8{

            margin-top:8mm;

        }

        .border-top{

            border-top:1px solid #bfc7cf;

        }

        /*************************************************
        PAGE BREAK
        *************************************************/

        .page-break{

            page-break-after:always;

        }

        /*************************************************
        PRINT
        *************************************************/

        @page{

            size:A4 portrait;

            margin:10mm;

        }


        /* Preview Browser */
        @media screen{

            .document{

                width:210mm;

                min-height:297mm;

                padding:10mm;

                margin:20px auto;

                background:#fff;

                box-shadow:0 10px 30px rgba(0,0,0,.12);

            }

        }

        /* Print & PDF */
        @media print{

            html{

                background:#ffffff;

            }

            body{

                background:#ffffff;

                margin:0;

                padding:0;

            }

            .document-wrapper{

                display:block;

                padding:0;

                background:#ffffff;

            }

            .document{

                width:auto;

                min-height:auto;

                border:none;

                box-shadow:none;

                margin:0;

                padding:0;

            }

        }

    </style>

</head>

<body>

<div class="document-wrapper">

    <div class="document">

    {{-- ============================================= --}}
    {{-- Corporate Header                              --}}
    {{-- ============================================= --}}

    <section class="section">

        @include(
            'filament.resources.purchase-orders.pages.partials.document-header'
        )

    </section>

    {{-- ============================================= --}}
    {{-- Purchase Order Header                         --}}
    {{-- ============================================= --}}

    <section class="section">

        @include(
            'filament.resources.purchase-orders.pages.partials.po-header'
        )

    </section>

    {{-- ============================================= --}}
    {{-- Item Table                                    --}}
    {{-- ============================================= --}}

    <section class="section">

        @include(
            'filament.resources.purchase-orders.pages.partials.po-items-table'
        )

    </section>

    {{-- ============================================= --}}
    {{-- Summary                                       --}}
    {{-- ============================================= --}}

    <section class="section">

        @include(
            'filament.resources.purchase-orders.pages.partials.po-summary'
        )

    </section>

    {{-- ============================================= --}}
    {{-- Signature                                     --}}
    {{-- ============================================= --}}

    <section class="section">

        @include(
            'filament.resources.purchase-orders.pages.partials.po-signature'
        )

    </section>

    {{-- ============================================= --}}
    {{-- Footer                                        --}}
    {{-- ============================================= --}}

    <section class="section">

        @include(
            'filament.resources.purchase-orders.pages.partials.po-footer'
        )

    </section>

</div>

</div>

@if($autoPrint)

<script>

window.onload=function(){

    window.print();

};

</script>

@endif

</body>

</html>