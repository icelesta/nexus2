<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceiptItem extends Model
{
    use SoftDeletes;

    protected $table = 'goods_receipt_items';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_item_id',
        'assignment_direct_market_item_id',
        'item_id',

        'received_qty',
        'uom_id',
        'warehouse_id',

        'accepted_qty',
        'rejected_qty',

        'remarks',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'goods_receipt_id'       => 'integer',
            'purchase_order_item_id' => 'integer',
            'assignment_direct_market_item_id' => 'integer',
            'item_id' => 'integer',
            'uom_id'                 => 'integer',
            'warehouse_id'           => 'integer',

            'received_qty'           => 'decimal:4',
            'accepted_qty'           => 'decimal:4',
            'rejected_qty'           => 'decimal:4',

            'created_by'             => 'integer',
            'updated_by'             => 'integer',
            'deleted_by'             => 'integer',

            'created_at'             => 'datetime',
            'updated_at'             => 'datetime',
            'deleted_at'             => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function assignmentDirectMarketItem(): BelongsTo
    {
        return $this->belongsTo(
            AssignmentDirectMarketItem::class,
            'assignment_direct_market_item_id'
        );
    }    

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('accepted_qty', '>', 0);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('rejected_qty', '>', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getPendingQtyAttribute(): float
    {
        return round(
            (float) $this->received_qty
            - (float) $this->accepted_qty
            - (float) $this->rejected_qty,
            4
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isFullyAccepted(): bool
    {
        return (float) $this->accepted_qty === (float) $this->received_qty;
    }

    public function hasRejectedQty(): bool
    {
        return (float) $this->rejected_qty > 0;
    }
}