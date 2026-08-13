<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestForQuotationItem extends Model
{
    use SoftDeletes;

    protected $table = 'request_for_quotation_items';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'request_for_quotation_id',
        'purchase_requisition_item_id',
        'item_id',

        'qty',
        'uom_id',

        'unit_price',
        'discount',
        'tax',
        'lead_time',
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
            'request_for_quotation_id' => 'integer',
            'purchase_requisition_item_id' => 'integer',
            'item_id' => 'integer',
            'uom_id' => 'integer',

            'qty' => 'decimal:4',
            'unit_price' => 'decimal:4',
            'discount' => 'decimal:4',
            'tax' => 'decimal:4',

            'lead_time' => 'integer',

            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',

            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function requestForQuotation(): BelongsTo
    {
        return $this->belongsTo(RequestForQuotation::class);
    }

    public function purchaseRequisitionItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisitionItem::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
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

    public function scopeWithSupplierPrice(Builder $query): Builder
    {
        return $query->where('unit_price', '>', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    public function getSubtotalAttribute(): float
    {
        return round(
            ((float) $this->qty * (float) $this->unit_price)
            - (float) $this->discount
            + (float) $this->tax,
            4
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    public function hasQuotation(): bool
    {
        return (float) $this->unit_price > 0;
    }
}