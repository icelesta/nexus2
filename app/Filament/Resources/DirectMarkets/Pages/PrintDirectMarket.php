<?php

declare(strict_types=1);

namespace App\Filament\Resources\DirectMarkets\Pages;

use App\Filament\Resources\DirectMarkets\DirectMarketResource;
use App\Models\DirectMarket;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

class PrintDirectMarket extends Page
{
    use InteractsWithRecord;

    protected static string $resource =
        DirectMarketResource::class;

    protected string $view =
        'filament.resources.direct-markets.pages.print-direct-market';

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->loadDirectMarketData();
    }

    /*
    |--------------------------------------------------------------------------
    | Load Direct Market Data
    |--------------------------------------------------------------------------
    */

    protected function loadDirectMarketData(): void
    {
        $this->record->load([
            'company',
            'businessUnit',
            'branch',
            'department',

            'costCenter',

            'requester',
            

            'items.item',
            'items.uom',
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
            $this->record->dm_no
            ?? 'Direct Market'
        );
    }

    public function getHeading(): string
    {
        return 'Direct Market';
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
            $this->record->dm_no
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
        $record = DirectMarket::query()
            ->with([
                'company',
                'businessUnit',
                'branch',
                'department',
                'costCenter',
                'requester',
                'items.item',
                'items.uom',
            ])
            ->findOrFail(
                $this->record->getKey()
            );

        $documentNumber = (string) (
            $record->dm_no ?? '-'
        );

        $documentDate = $record->request_date
            ? \Carbon\Carbon::parse(
                $record->request_date
            )->format('d M Y')
            : '-';

        $documentStatus = strtoupper(
            (string) (
                $record->status ?? '-'
            )
        );

        $pdf = Pdf::loadView(
            'filament.resources.direct-markets.pages.print-direct-market-pdf',
            [
                'record' => $record,

                'documentNumber' =>
                    $documentNumber,

                'documentDate' =>
                    $documentDate,

                'documentStatus' =>
                    $documentStatus,
            ]
        );

        $pdf->setPaper(
            'a4',
            'portrait'
        );

        return $pdf->download(
            $this->sanitizePdfFilename(
                $documentNumber
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
        );

        if (
            ! is_string($filename) ||
            $filename === ''
        ) {
            $filename = 'direct-market.pdf';
        }

        $filename = trim(
            $filename,
            '-.'
        );

        if ($filename === '') {
            $filename = 'direct-market';
        }

        return $filename . '.pdf';
    }

    /*
    |--------------------------------------------------------------------------
    | Download PDF
    |--------------------------------------------------------------------------
    */

    public function downloadPdf(
        int|string $record
    ): Response {
        $this->record =
            $this->resolveRecord($record);

        return $this->exportPdf();
    }
}