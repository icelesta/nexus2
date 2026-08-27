<?php

declare(strict_types=1);

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Services\Purchasing\PurchaseOrderPrintService;

use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Contracts\View\View;

use Symfony\Component\HttpFoundation\Response;


class PurchaseOrderPrintController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Preview / Print / PDF View
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | Preview and PDF intentionally use the SAME Blade view.
    |
    | This keeps the Purchase Order document design centralized.
    |
    */

    private const DOCUMENT_VIEW =
        'filament.resources.purchase-orders.pages.partials.preview-purchase-order';

    private const PDF_DOCUMENT_VIEW =
    'filament.resources.purchase-orders.pages.partials.print-layout';


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
     * Display Purchase Order Preview.
     *
     * This is the MASTER visual document.
     */
    public function preview(
        PurchaseOrder $purchaseOrder,
    ): View {

        $data = $this->printService->build(
            $purchaseOrder,
        );

        return view(
            self::DOCUMENT_VIEW,
            array_merge(
                $data,
                [
                    'autoPrint' => false,
                ],
            ),
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Browser Print
    |--------------------------------------------------------------------------
    */

    /**
     * Display Purchase Order for browser printing.
     *
     * Uses the SAME document view as Preview.
     */
    public function print(
        PurchaseOrder $purchaseOrder,
    ): View {

        $data = $this->printService->build(
            $purchaseOrder,
        );

        return view(
            self::DOCUMENT_VIEW,
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
     * Export Purchase Order as PDF.
     *
     * IMPORTANT:
     *
     * PDF uses the EXACT SAME Blade view as Preview.
     *
     * We intentionally do NOT use:
     *
     * print-layout.blade.php
     *
     * because Preview is the single source of truth
     * for the Purchase Order visual document.
     */
        public function export(
            PurchaseOrder $purchaseOrder,
        ): Response {

        /*
        |--------------------------------------------------------------------------
        | Build PO document data
        |--------------------------------------------------------------------------
        */

        $data = $this->printService->build(
            $purchaseOrder,
        );


        /*
        |--------------------------------------------------------------------------
        | PDF Data
        |--------------------------------------------------------------------------
        |
        | autoPrint must remain disabled for PDF rendering.
        |
        */

        $data['autoPrint'] = false;


        /*
        |--------------------------------------------------------------------------
        | Generate PDF
        |--------------------------------------------------------------------------
        |
        | SAME VIEW AS PREVIEW
        |
        */

        $pdf = Pdf::loadView(
            self::PDF_DOCUMENT_VIEW,
            $data,
        );

        /*
        |--------------------------------------------------------------------------
        | DomPDF Rendering Options
        |--------------------------------------------------------------------------
        |
        | These options improve HTML/CSS compatibility without
        | changing the PO data or business logic.
        |
        */

        $pdf->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultFont'          => 'Arial',
            'dpi'                  => 96,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Paper
        |--------------------------------------------------------------------------
        */

        $pdf->setPaper(
            'A4',
            'portrait',
        );


        /*
        |--------------------------------------------------------------------------
        | File Name
        |--------------------------------------------------------------------------
        */

        $fileName = preg_replace(
            '/[^A-Za-z0-9._-]/',
            '-',
            (string) $purchaseOrder->document_no,
        );


        $fileName = trim(
            (string) $fileName,
            '-',
        );


        /*
        |--------------------------------------------------------------------------
        | Download
        |--------------------------------------------------------------------------
        */

        $pdfContent = $pdf->output();

        return response(
            $pdfContent,
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.pdf"',
                'Content-Length'      => strlen($pdfContent),
            ],
        );
    }
}