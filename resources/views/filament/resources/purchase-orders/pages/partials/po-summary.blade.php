{{-- ==========================================================
| Purchase Order Financial Summary
| Nexus ERP 2.0
========================================================== --}}

<style>

.po-summary{

    width:100%;

    margin-top:10px;

}

.po-summary-table{

    width:100%;

    max-width:none;

    margin-left:0;

    margin-right:0;

    border-collapse:collapse;

}

.po-summary-table td{

    border:1px solid #d9d9d9;

    padding:8px 10px;

    font-size:13px;

}

.summary-label{

    background:#f9fafb;

    font-weight:600;

    color:#374151;

}

.summary-value{

    text-align:right;

    font-weight:500;

}

.summary-grand{

    background:#2563eb;

    color:#ffffff;

    font-size:15px;

    font-weight:700;

}


/* ==========================================================
   REMARK + FINANCIAL SUMMARY LAYOUT
   ========================================================== */

.po-summary-layout{

    width:100%;

    border-collapse:collapse;

}

.po-summary-layout > tbody > tr > td{

    border:none;

    padding:0;

    vertical-align:top;

}


/* ==========================================================
   REMARK
   ========================================================== */

.po-remark-cell{

    width:50%;

    padding-right:5px !important;

    text-align:left !important;

    vertical-align:top !important;

}

.po-remark-box{

    border:1px solid #d9d9d9;

    height:auto;

    min-height:0;

    padding:10px 12px;

    font-size:13px;

    color:#111827;

    box-sizing:border-box;

    text-align:left !important;

}


.po-remark-title{

    display:block !important;

    width:100% !important;

    margin:0 0 10px 0 !important;

    padding:0 !important;

    font-size:15px;

    font-weight:700;

    text-transform:uppercase;

    text-align:left !important;

    vertical-align:top !important;

}

.po-remark-content{

    display:block !important;

    width:100% !important;

    margin:0 !important;

    padding:0 !important;

    text-align:left !important;

    vertical-align:top !important;

    white-space:pre-wrap;

    word-break:break-word;

    float:none !important;

}


/* ==========================================================
   FINANCIAL SUMMARY
   ========================================================== */

.po-summary-cell{

    width:50%;

    padding-left:5px !important;

    text-align:left !important;

    vertical-align:top !important;

}


/* ==========================================================
   PRINT
   ========================================================== */

@media print{

    .po-summary-layout{

        width:100% !important;

    }

    .po-remark-cell{

        width:50% !important;

        text-align:left !important;

        vertical-align:top !important;

    }

    .po-remark-box{

        height:auto !important;

    }


    .po-remark-title{

        width:100% !important;

        text-align:left !important;

    }

    .po-remark-content{

        width:100% !important;

        text-align:left !important;

    }

    .po-summary-cell{

        width:50% !important;

        text-align:left !important;

        vertical-align:top !important;

    }


    .po-summary-table{

        width:100% !important;

        max-width:none !important;

        margin-left:0 !important;

        margin-right:0 !important;

    }

}

</style>


<div class="po-summary">

    <table
        class="po-summary-layout"
        style="text-align:left !important;"
    >

        <tr>

            {{-- ==================================================
                 REMARK
            ================================================== --}}

            <td
                class="po-remark-cell"
                style="text-align:left !important; vertical-align:top !important;"
            >

            </td>


            {{-- ==================================================
                 FINANCIAL SUMMARY
            ================================================== --}}

            <td class="po-summary-cell">

                <table class="po-summary-table">

                    {{-- CURRENCY --}}

                    <tr>

                        <td class="summary-label">
                            Currency
                        </td>

                        <td class="summary-value">
                            {{ $purchaseOrder->supplier?->currency_code ?? 'IDR' }}
                        </td>

                    </tr>


                    {{-- SUBTOTAL --}}

                    <tr>

                        <td class="summary-label">
                            Subtotal
                        </td>

                        <td class="summary-value">
                            {{ number_format((float) $subtotal, 2) }}
                        </td>

                    </tr>


                    {{-- DISCOUNT --}}

                    <tr>

                        <td class="summary-label">
                            Discount
                        </td>

                        <td class="summary-value">
                            {{ number_format((float) $discountAmount, 2) }}
                        </td>

                    </tr>


                    {{-- TAX --}}

                    <tr>

                        <td class="summary-label">
                            Tax
                        </td>

                        <td class="summary-value">
                            {{ number_format((float) $taxAmount, 2) }}
                        </td>

                    </tr>


                    {{-- GRAND TOTAL --}}

                    <tr>

                        <td class="summary-label summary-grand">
                            Grand Total
                        </td>

                        <td class="summary-value summary-grand">
                            {{ number_format((float) $grandTotal, 2) }}
                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>

</div>