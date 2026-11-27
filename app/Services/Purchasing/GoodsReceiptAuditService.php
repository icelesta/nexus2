<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptAudit;
use App\Models\GoodsReceiptItem;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class GoodsReceiptAuditService
{
    /*
    |--------------------------------------------------------------------------
    | Event Types
    |--------------------------------------------------------------------------
    */

    public const EVENT_CREATE = 'CREATE';
    public const EVENT_SAVE = 'SAVE';
    public const EVENT_SUBMIT = 'SUBMIT';
    public const EVENT_QC_UPDATE = 'QC_UPDATE';
    public const EVENT_CORRECTION = 'CORRECTION';

    public const EVENT_CANCEL_REQUEST = 'CANCEL_REQUEST';
    public const EVENT_CANCEL_APPROVED = 'CANCEL_APPROVED';
    public const EVENT_CANCEL_REJECTED = 'CANCEL_REJECTED';

    public const EVENT_STATUS_CHANGE = 'STATUS_CHANGE';
    public const EVENT_DELETE_DRAFT = 'DELETE_DRAFT';

    /*
    |--------------------------------------------------------------------------
    | Record Audit
    |--------------------------------------------------------------------------
    */

    public function record(
        GoodsReceipt $goodsReceipt,
        string $eventType,
        ?GoodsReceiptItem $goodsReceiptItem = null,
        ?string $fieldName = null,
        mixed $oldValue = null,
        mixed $newValue = null,
        ?string $reason = null,
        array $metadata = [],
        ?int $userId = null,
    ): GoodsReceiptAudit {
        $eventType = strtoupper(trim($eventType));

        if ($eventType === '') {
            throw new InvalidArgumentException(
                'Goods Receipt audit event type cannot be empty.'
            );
        }

        if (strlen($eventType) > 60) {
            throw new InvalidArgumentException(
                'Goods Receipt audit event type cannot exceed 60 characters.'
            );
        }

        if (
            $goodsReceiptItem !== null
            && (int) $goodsReceiptItem->goods_receipt_id
                !== (int) $goodsReceipt->getKey()
        ) {
            throw new InvalidArgumentException(
                'The audit item does not belong to the specified Goods Receipt.'
            );
        }

        $actorId = $userId ?? Auth::id();

        return GoodsReceiptAudit::query()->create([
            'goods_receipt_id' => $goodsReceipt->getKey(),

            'goods_receipt_item_id' => $goodsReceiptItem?->getKey(),

            'event_type' => $eventType,

            'field_name' => $fieldName,

            'old_value' => $this->normalizeValue($oldValue),

            'new_value' => $this->normalizeValue($newValue),

            'reason' => $reason,

            'metadata' => $metadata !== []
                ? $metadata
                : null,

            'user_id' => $actorId,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Record Header Creation
    |--------------------------------------------------------------------------
    */

    public function recordCreated(
        GoodsReceipt $goodsReceipt,
        ?int $userId = null,
    ): GoodsReceiptAudit {
        return $this->record(
            goodsReceipt: $goodsReceipt,
            eventType: self::EVENT_CREATE,
            newValue: [
                'grn_no' => $goodsReceipt->grn_no,
                'status' => $goodsReceipt->status,
                'items_status' => $goodsReceipt->items_status,
                'rr_status' => $goodsReceipt->rr_status,
            ],
            metadata: [
                'source' => 'goods_receipt',
                'action' => 'header_created',
            ],
            userId: $userId,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Record Status Changes
    |--------------------------------------------------------------------------
    */

    public function recordStatusChange(
        GoodsReceipt $goodsReceipt,
        string $fieldName,
        mixed $oldValue,
        mixed $newValue,
        ?GoodsReceiptItem $goodsReceiptItem = null,
        array $metadata = [],
        ?int $userId = null,
    ): GoodsReceiptAudit {
        return $this->record(
            goodsReceipt: $goodsReceipt,
            eventType: self::EVENT_STATUS_CHANGE,
            goodsReceiptItem: $goodsReceiptItem,
            fieldName: $fieldName,
            oldValue: $oldValue,
            newValue: $newValue,
            metadata: $metadata,
            userId: $userId,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Record QC Changes
    |--------------------------------------------------------------------------
    */

    public function recordQcUpdate(
        GoodsReceipt $goodsReceipt,
        GoodsReceiptItem $goodsReceiptItem,
        bool $oldValue,
        bool $newValue,
        ?int $userId = null,
    ): GoodsReceiptAudit {
        return $this->record(
            goodsReceipt: $goodsReceipt,
            eventType: self::EVENT_QC_UPDATE,
            goodsReceiptItem: $goodsReceiptItem,
            fieldName: 'qc_passed',
            oldValue: $oldValue,
            newValue: $newValue,
            metadata: [
                'qc_result' => $newValue ? 'PASSED' : 'UNPASSED',
            ],
            userId: $userId,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Audit Values
    |--------------------------------------------------------------------------
    */

    protected function normalizeValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode(
            $value,
            JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
        );
    }
}