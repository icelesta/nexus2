{{-- ===========================================================
 | Nexus ERP 2.0
 | Purchase Order Preview
 | Standalone Document View
 ============================================================ --}}

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $purchaseOrder->document_no }}
    </title>

</head>


<body>

    @include(
        'filament.resources.purchase-orders.pages.partials.print-layout',
        [
            'purchaseOrder'   => $purchaseOrder,
            'company'         => $company,
            'supplier'        => $supplier,
            'businessUnit'    => $businessUnit,
            'branch'          => $branch,
            'department'      => $department,
            'section'         => $section,
            'costCenter'      => $costCenter,
            'warehouse'       => $warehouse,
            'currency'        => $currency,
            'requester'       => $requester,
            'approvedBy'      => $approvedBy,
            'generatedBy'     => $generatedBy,
            'items'           => $items,
            'subtotal'        => $subtotal,
            'discountAmount'  => $discountAmount,
            'taxAmount'       => $taxAmount,
            'grandTotal'      => $grandTotal,
            'autoPrint'       => $autoPrint ?? false,
        ]
    )

</body>

</html>