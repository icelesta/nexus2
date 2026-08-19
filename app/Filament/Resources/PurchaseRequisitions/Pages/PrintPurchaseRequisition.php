<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Pages;

use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

class PrintPurchaseRequisition extends Page
{
    use InteractsWithRecord;

    protected static string $resource =
        PurchaseRequisitionResource::class;

    protected string $view =
        'filament.resources.purchase-requisitions.pages.print-purchase-requisition';

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->loadPurchaseRequisitionData();
    }

    /*
    |--------------------------------------------------------------------------
    | Load MR + AMR Commercial Data
    |--------------------------------------------------------------------------
    */

    protected function loadPurchaseRequisitionData(): void
    {
        $this->record->load([
            'company',
            'businessUnit',
            'branch',
            'department',
            'section',
            'costCenter',
            'warehouse',

            'requester',
            'createdBy',

            'items.item',
            'items.uom',
            'items.warehouse',

            /*
            |--------------------------------------------------------------------------
            | AMR Commercial Synchronization
            |--------------------------------------------------------------------------
            */

            'items.latestAssignmentMaterialRequisitionItem.supplier',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Page Information
    |--------------------------------------------------------------------------
    */

    public function getTitle(): string
    {
        return (string) (
            $this->record->pr_no
            ?? 'Material Requisition'
        );
    }

    public function getHeading(): string
    {
        return 'Material Requisition';
    }

    public function getBreadcrumb(): string
    {
        return 'Preview';
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    /*
    |--------------------------------------------------------------------------
    | Document Information
    |--------------------------------------------------------------------------
    */

    public function getDocumentNumber(): string
    {
        return (string) (
            $this->record->pr_no
            ?? '-'
        );
    }

    public function getDocumentDate(): string
    {
        return $this->record->request_date
            ? $this->record->request_date->format('d M Y')
            : '-';
    }

    public function getDocumentStatus(): string
    {
        return strtoupper(
            (string) (
                $this->record->status
                ?? '-'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Export PDF
    |--------------------------------------------------------------------------
    */

    public function exportPdf(): Response
    {
        $record = $this->record->fresh();

        $record->load([
            'company',
            'businessUnit',
            'branch',
            'department',
            'section',
            'costCenter',
            'warehouse',

            'requester',
            'createdBy',

            'items.item',
            'items.uom',
            'items.warehouse',

            /*
            |--------------------------------------------------------------------------
            | AMR Commercial Synchronization
            |--------------------------------------------------------------------------
            */

            'items.latestAssignmentMaterialRequisitionItem.supplier',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Generate PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'filament.resources.purchase-requisitions.pages.print-purchase-requisition',
            [
                'record' => $record,

                /*
                |--------------------------------------------------------------------------
                | Compatibility
                |--------------------------------------------------------------------------
                |
                | Blade lama masih mungkin membaca variable ini.
                | Commercial data sekarang berasal langsung dari
                | each MR Item relationship.
                |
                */

                'assignmentItems' => [],

                'pdfMode' => true,

                'documentNumber' => $this->getDocumentNumber(),

                'documentDate' => $this->getDocumentDate(),

                'documentStatus' => $this->getDocumentStatus(),
            ]
        );

        $pdf->setPaper(
            'a4',
            'portrait'
        );

        return $pdf->download(
            $this->sanitizePdfFilename(
                $this->getDocumentNumber() . '.pdf'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PDF Filename
    |--------------------------------------------------------------------------
    */

    protected function sanitizePdfFilename(
        string $filename
    ): string {
        $filename = preg_replace(
            '/[^\pL\pN\-_\.]+/u',
            '-',
            $filename
        ) ?? 'material-requisition.pdf';

        return trim(
            $filename,
            '-'
        );
    }
}