<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Pages;

use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

class PrintPurchaseRequisition extends Page
{
    use InteractsWithRecord;

    protected static string $resource =
        PurchaseRequisitionResource::class;

    protected string $view =
        'filament.resources.purchase-requisitions.pages.print-purchase-requisition';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

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
        ]);
    }

    public function getTitle(): string
    {
        return (string) ($this->record->pr_no ?? 'Material Requisition');
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

    public function getDocumentNumber(): string
    {
        return (string) ($this->record->pr_no ?? '-');
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
            (string) ($this->record->status ?? '-')
        );
    }

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
        ]);

        $documentNumber = (string) ($record->pr_no ?? '-');

        $documentDate = $record->request_date
            ? $record->request_date->format('d M Y')
            : '-';

        $documentStatus = strtoupper(
            (string) ($record->status ?? '-')
        );

        $pdf = Pdf::loadView(
            'filament.resources.purchase-requisitions.pages.print-purchase-requisition',
            [
                'record' => $record,

                'pdfMode' => true,

                'documentNumber' => $documentNumber,

                'documentDate' => $documentDate,

                'documentStatus' => $documentStatus,
            ]
        );

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(
            $this->sanitizePdfFilename(
                $documentNumber . '.pdf'
            )
        );
    }

    protected function sanitizePdfFilename(string $filename): string
    {
        $filename = preg_replace(
            '/[^\pL\pN\-_\.]+/u',
            '-',
            $filename
        ) ?? 'material-requisition.pdf';

        return trim($filename, '-');
    }
}