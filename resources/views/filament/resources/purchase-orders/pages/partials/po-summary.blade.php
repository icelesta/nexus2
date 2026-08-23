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

    width:380px;

    max-width:380px;

    margin-left:auto;

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
   PRINT
   Prevent global print table rule from stretching summary
   ========================================================== */

@media print{

    .po-summary-table{

        width:380px !important;

        max-width:380px !important;

        margin-left:auto !important;

        margin-right:0 !important;

    }

}

</style>


<div class="po-summary">

    <table class="po-summary-table">

        <tr>

            <td class="summary-label">
                Currency
            </td>

            <td class="summary-value">
                {{ $purchaseOrder->supplier?->currency_code ?? 'IDR' }}
            </td>

        </tr>


        <tr>

            <td class="summary-label">
                Subtotal
            </td>

            <td class="summary-value">
                {{ number_format((float) $subtotal, 2) }}
            </td>

        </tr>


        <tr>

            <td class="summary-label">
                Discount
            </td>

            <td class="summary-value">
                {{ number_format((float) $discountAmount, 2) }}
            </td>

        </tr>


        <tr>

            <td class="summary-label">
                Tax
            </td>

            <td class="summary-value">
                {{ number_format((float) $taxAmount, 2) }}
            </td>

        </tr>


        <tr>

            <td class="summary-label summary-grand">
                Grand Total
            </td>

            <td class="summary-value summary-grand">
                {{ number_format((float) $grandTotal, 2) }}
            </td>

        </tr>

    </table>

</div>