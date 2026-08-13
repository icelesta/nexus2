<?php

declare(strict_types=1);

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Services\Purchasing\PurchaseOrderPrintService;
use Illuminate\Contracts\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class PurchaseOrderPrintController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(
        protected PurchaseOrderPrintService $printService,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Preview Purchase Order
    |--------------------------------------------------------------------------
    */

    /**
     * Display Purchase Order preview.
     */
    public function preview(
        PurchaseOrder $purchaseOrder,
    ): View {

        $data = $this->printService->build($purchaseOrder);

        return view(
            'filament.resources.purchase-orders.pages.partials.preview-purchase-order',
            $data,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Print Purchase Order
    |--------------------------------------------------------------------------
    */

    /**
     * Browser Print.
     *
     * Sprint PO-UI-2.2
     */
    public function print(
        PurchaseOrder $purchaseOrder,
    ): View {

        $data = $this->printService->build($purchaseOrder);

        return view(
            'filament.resources.purchase-orders.pages.partials.preview-purchase-order',
            array_merge(
                $data,
                [
                    'autoPrint' => true,
                ],
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Export PDF
    |--------------------------------------------------------------------------
    */


    /**
     * Export Purchase Order PDF.
     *
     * Sprint PO-UI-2.3
     */
    public function export(
        PurchaseOrder $purchaseOrder,
    ): Response {

        $data = $this->printService->build(
            $purchaseOrder,
        );

        $pdf = Pdf::loadView(
            'filament.resources.purchase-orders.pages.partials.preview-purchase-order',
            $data,
        );

        $pdf->setPaper(
            'a4',
            'portrait',
        );

        $fileName = preg_replace(
            '/[^A-Za-z0-9._-]/',
            '-',
            $purchaseOrder->document_no,
        );

        return $pdf->download(
            "{$fileName}.pdf",
        );
    }
}