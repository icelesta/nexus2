<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'purchase_order_items';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | References
        |--------------------------------------------------------------------------
        */

        'purchase_order_id',
        'assignment_material_requisition_item_id',

        /*
        |--------------------------------------------------------------------------
        | Item Snapshot
        |--------------------------------------------------------------------------
        */

        'item_id',
        'item_code',
        'item_name',
        'item_description',
        'specification',

        /*
        |--------------------------------------------------------------------------
        | Quantity
        |--------------------------------------------------------------------------
        */

        'ordered_qty',
        'received_qty',
        'remaining_qty',

        /*
        |--------------------------------------------------------------------------
        | UOM Snapshot
        |--------------------------------------------------------------------------
        */

        'uom_id',
        'uom_code',
        'uom_name',

        /*
        |--------------------------------------------------------------------------
        | Warehouse Snapshot
        |--------------------------------------------------------------------------
        */

        'warehouse_id',
        'warehouse_code',
        'warehouse_name',

        /*
        |--------------------------------------------------------------------------
        | Supplier
        |--------------------------------------------------------------------------
        */

        'supplier_id',

        /*
        |--------------------------------------------------------------------------
        | Pricing
        |--------------------------------------------------------------------------
        */

        'unit_price',

        'discount_percent',
        'discount_amount',

        'gross_amount',
        'net_amount',

        /*
        |--------------------------------------------------------------------------
        | Tax
        |--------------------------------------------------------------------------
        */

        'tax_id',
        'tax_name',
        'tax_percent',
        'tax_amount',

        /*
        |--------------------------------------------------------------------------
        | Totals
        |--------------------------------------------------------------------------
        */

        'line_total',
        'grand_total',

        /*
        |--------------------------------------------------------------------------
        | Delivery
        |--------------------------------------------------------------------------
        */

        'required_date',
        'delivery_date',
        'delivery_location',

        /*
        |--------------------------------------------------------------------------
        | Workflow
        |--------------------------------------------------------------------------
        */

        'status',

        'remarks',

        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        'created_by',
        'updated_by',
        'deleted_by',
    ];


    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */

            'ordered_qty'      => 'decimal:4',
            'received_qty'     => 'decimal:4',
            'remaining_qty'    => 'decimal:4',

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */

            'unit_price'         => 'decimal:4',

            'discount_percent'   => 'decimal:2',
            'discount_amount'    => 'decimal:4',

            'gross_amount'       => 'decimal:2',
            'net_amount'         => 'decimal:2',

            /*
            |--------------------------------------------------------------------------
            | Tax
            |--------------------------------------------------------------------------
            */

            'tax_percent'      => 'decimal:2',
            'tax_amount'       => 'decimal:4',

            /*
            |--------------------------------------------------------------------------
            | Totals
            |--------------------------------------------------------------------------
            */

            'line_total'       => 'decimal:4',
            'grand_total'      => 'decimal:2',

            /*
            |--------------------------------------------------------------------------
            | Delivery
            |--------------------------------------------------------------------------
            */

            'required_date'    => 'date',
            'delivery_date'    => 'date',

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            'created_at'       => 'datetime',
            'updated_at'       => 'datetime',
            'deleted_at'       => 'datetime',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_OPEN = 'Open';

    public const STATUS_PARTIALLY_RECEIVED = 'Partially Received';

    public const STATUS_RECEIVED = 'Received';

    public const STATUS_CANCELLED = 'Cancelled';


    /*
    |--------------------------------------------------------------------------
    | Status Options
    |--------------------------------------------------------------------------
    */

    public static function getStatusOptions(): array
    {
        return [

            self::STATUS_OPEN                 => 'Open',

            self::STATUS_PARTIALLY_RECEIVED   => 'Partially Received',

            self::STATUS_RECEIVED             => 'Received',

            self::STATUS_CANCELLED            => 'Cancelled',

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Purchase Order Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Purchase Order Header.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(
            PurchaseOrder::class,
            'purchase_order_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Source Document
    |--------------------------------------------------------------------------
    */

    /**
     * Assignment Material Requisition Item.
     */
    public function assignmentMaterialRequisitionItem(): BelongsTo
    {
        return $this->belongsTo(
            AssignmentMaterialRequisitionItem::class,
            'assignment_material_requisition_item_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Item
    |--------------------------------------------------------------------------
    */

    /**
     * Item Master.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(
            Item::class,
            'item_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Unit of Measure
    |--------------------------------------------------------------------------
    */

    /**
     * Unit of Measure.
     */
    public function uom(): BelongsTo
    {
        return $this->belongsTo(
            Uom::class,
            'uom_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Warehouse
    |--------------------------------------------------------------------------
    */

    /**
     * Warehouse.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(
            Warehouse::class,
            'warehouse_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Supplier
    |--------------------------------------------------------------------------
    */

    /**
     * Supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(
            Supplier::class,
            'supplier_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Tax
    |--------------------------------------------------------------------------
    */

    /**
     * Tax.
     */
    //public function tax(): BelongsTo
    //{
    //    return $this->belongsTo(
    //        Tax::class,
    //        'tax_id'
    //    );
    //}


    /*
    |--------------------------------------------------------------------------
    | Audit
    |--------------------------------------------------------------------------
    */

    /**
     * Created By.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * Updated By.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    /**
     * Deleted By.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'deleted_by'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Open Items.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Partially Received Items.
     */
    public function scopePartiallyReceived(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_PARTIALLY_RECEIVED
        );
    }

    /**
     * Fully Received Items.
     */
    public function scopeReceived(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_RECEIVED
        );
    }

    /**
     * Cancelled Items.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_CANCELLED
        );
    }

    /*
    |--------------------------------------------------------------------------
    | State Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check whether item is open.
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Check whether item is partially received.
     */
    public function isPartiallyReceived(): bool
    {
        return $this->status === self::STATUS_PARTIALLY_RECEIVED;
    }

    /**
     * Check whether item is fully received.
     */
    public function isReceived(): bool
    {
        return $this->status === self::STATUS_RECEIVED;
    }

    /**
     * Check whether item is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the item can be edited.
     */
    public function canEdit(): bool
    {
        return $this->isOpen();
    }

    /**
     * Determine whether the item can be deleted.
     */
    public function canDelete(): bool
    {
        return $this->isOpen();
    }

    /**
     * Determine whether the item can be received.
     */
    public function canReceive(): bool
    {
        return $this->isOpen()
            || $this->isPartiallyReceived();
    }

    /**
     * Determine whether the item can be cancelled.
     */
    public function canCancel(): bool
    {
        return ! $this->isReceived()
            && ! $this->isCancelled();
    }

    /*
    |--------------------------------------------------------------------------
    | Lock Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the item is locked.
     */
    public function isLocked(): bool
    {
        return ! $this->isOpen();
    }



    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    public function getCalculatedLineTotalAttribute(): float
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

    public function hasValue(): bool
    {
        return (float) $this->line_total > 0;
    }
}