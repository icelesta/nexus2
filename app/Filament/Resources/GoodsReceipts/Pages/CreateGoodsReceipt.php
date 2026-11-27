<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts\Pages;

use App\Filament\Resources\GoodsReceipts\GoodsReceiptResource;
use App\Services\Purchasing\GoodsReceiptService;
use Filament\Resources\Pages\CreateRecord;

class CreateGoodsReceipt extends CreateRecord
{
    protected static string $resource = GoodsReceiptResource::class;

    public function getTitle(): string
    {
        return 'Create Receiving Record';
    }

    public function getHeading(): string
    {
        return 'Create Receiving Record';
    }

    public function getSubheading(): ?string
    {
        return 'Select Supplier, Source Type, and the eligible source document to create a Draft Receiving Record.';
    }

    public function getBreadcrumb(): string
    {
        return 'Create';
    }

    protected function handleRecordCreation(
        array $data
    ): \App\Models\GoodsReceipt {
        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        $supplierId = isset($data['supplier_id'])
            ? (int) $data['supplier_id']
            : null;

        /*
        |--------------------------------------------------------------------------
        | SOURCE TYPE
        |--------------------------------------------------------------------------
        */

        $sourceType = $data['source_type'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | SOURCE ID
        |--------------------------------------------------------------------------
        */

        $sourceId = isset($data['source_id'])
            ? (int) $data['source_id']
            : null;

        /*
        |--------------------------------------------------------------------------
        | REMARK
        |--------------------------------------------------------------------------
        */

        $remarks = filled($data['remarks'] ?? null)
            ? trim((string) $data['remarks'])
            : null;

        /*
        |--------------------------------------------------------------------------
        | EXTERNAL DOCUMENT NUMBER
        |--------------------------------------------------------------------------
        */

        $documentNumber = filled($data['document_number'] ?? null)
            ? trim((string) $data['document_number'])
            : null;

        /*
        |--------------------------------------------------------------------------
        | BASIC VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($supplierId === null || $supplierId <= 0) {
            throw new \RuntimeException(
                'Supplier is required.'
            );
        }

        if ($sourceId === null || $sourceId <= 0) {
            throw new \RuntimeException(
                'Source document is required.'
            );
        }

        if (! in_array(
            $sourceType,
            [
                'PURCHASE_ORDER',
                'ASSIGNMENT_DIRECT_MARKET',
            ],
            true
        )) {
            throw new \RuntimeException(
                'Please select a valid Receiving Record source type.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | GOODS RECEIPT SERVICE
        |--------------------------------------------------------------------------
        */

        $service = app(GoodsReceiptService::class);

        /*
        |--------------------------------------------------------------------------
        | PURCHASE ORDER
        |--------------------------------------------------------------------------
        */

        if ($sourceType === 'PURCHASE_ORDER') {
            return $service->createHeaderFromPurchaseOrder(
                $sourceId,
                auth()->id(),
                $supplierId,
                $remarks,
                $documentNumber,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ASSIGNMENT DIRECT MARKET
        |--------------------------------------------------------------------------
        */

        if ($sourceType === 'ASSIGNMENT_DIRECT_MARKET') {
            return $service->createHeaderFromAssignmentDirectMarket(
                $sourceId,
                auth()->id(),
                $supplierId,
                $remarks,
                $documentNumber,
            );
            /*
            |--------------------------------------------------------------------------
            | SUPPLIER-SPECIFIC RR
            |--------------------------------------------------------------------------
            |
            | Karena Supplier sudah dipilih di Create RR,
            | service seharusnya menghasilkan tepat 1 RR.
            |
            */

            if ($receipts->count() !== 1) {
                throw new \RuntimeException(
                    'The selected Assignment Direct Market does not produce exactly one Receiving Record for the selected Supplier.'
                );
            }

            return $receipts->first();
        }

        /*
        |--------------------------------------------------------------------------
        | SAFETY FALLBACK
        |--------------------------------------------------------------------------
        */

        throw new \RuntimeException(
            'Please select a valid Receiving Record source.'
        );
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Receiving Record created successfully.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', [
            'record' => $this->getRecord(),
        ]);
    }
}