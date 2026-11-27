<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceiptAudit extends Model
{
    protected $table = 'goods_receipt_audits';

    // Audit menggunakan created_at saja.
    public const UPDATED_AT = null;

    protected $fillable = [
        'goods_receipt_id',
        'goods_receipt_item_id',
        'event_type',
        'field_name',
        'old_value',
        'new_value',
        'reason',
        'metadata',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'goods_receipt_id' => 'integer',
            'goods_receipt_item_id' => 'integer',
            'user_id' => 'integer',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(
            GoodsReceipt::class,
            'goods_receipt_id'
        );
    }

    public function goodsReceiptItem(): BelongsTo
    {
        return $this->belongsTo(
            GoodsReceiptItem::class,
            'goods_receipt_item_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}