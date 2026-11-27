<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InventoryStockMovement extends Model
{
    protected $table = 'inventory_stock_movements';

    protected $guarded = [];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'warehouse_id' => 'integer',
            'goods_receipt_id' => 'integer',
            'goods_receipt_item_id' => 'integer',
            'reference_id' => 'integer',
            'created_by' => 'integer',

            'quantity' => 'decimal:4',
            'balance_before' => 'decimal:4',
            'balance_after' => 'decimal:4',

            'purchase_unit_cost' => 'decimal:4',
            'purchase_currency_code' => 'string',
            'exchange_rate' => 'decimal:6',
            'unit_cost_idr' => 'decimal:4',
            'avg_cost_before' => 'decimal:4',
            'avg_cost_after' => 'decimal:4',

            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $movement): void {
            if (empty($movement->uuid)) {
                $movement->uuid = (string) Str::uuid();
            }
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function goodsReceiptItem(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}