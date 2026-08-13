<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\AssignmentMaterialRequisitionItem;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TaxMaster;

class AssignmentMaterialRequisitionItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'assignment_material_requisition_items';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'assignment_material_requisition_id',
        'purchase_requisition_item_id',

        /*
        |--------------------------------------------------------------------------
        | Snapshot Item
        |--------------------------------------------------------------------------
        */

        'item_id',
        'item_code',
        'item_name',
        'item_description',
        'specification',

        /*
        |--------------------------------------------------------------------------
        | Snapshot UOM
        |--------------------------------------------------------------------------
        */

        'uom_id',
        'uom_code',
        'uom_name',

        /*
        |--------------------------------------------------------------------------
        | Snapshot Quantity
        |--------------------------------------------------------------------------
        */

        'requested_qty',
        'approved_qty',
        'remaining_qty',

        /*
        |--------------------------------------------------------------------------
        | Snapshot Warehouse
        |--------------------------------------------------------------------------
        */

        'warehouse_id',
        'warehouse_code',
        'warehouse_name',

        /*
        |--------------------------------------------------------------------------
        | Requirement
        |--------------------------------------------------------------------------
        */

        'required_date',
        'delivery_location',


        'supplier_id',

        'assigned_qty',

        'unit_price',
        'quotation_number',
        'quotation_date',

        'lead_time_days',
        'delivery_date',

        'discount_percent',
        'discount_amount',

        'tax_id',
        'tax_name',

        'tax_percent',
        'tax_amount',

        'line_total',

        'grand_total',

        'buyer_notes',

        'is_selected_supplier',

        'status',
        'approval_status',

        'remarks',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'assigned_qty'         => 'decimal:4',
            'unit_price'         => 'decimal:2',

            'discount_percent'     => 'decimal:2',
            'discount_amount'      => 'decimal:2',

            'tax_percent'           => 'decimal:2',
            'tax_amount'            => 'decimal:2',

            'line_total'            => 'decimal:2',

            'grand_total'           => 'decimal:2',

            'quotation_date'       => 'date',
            'delivery_date'        => 'date',

            'is_selected_supplier' => 'boolean',

            'requested_qty' => 'decimal:4',
            'approved_qty' => 'decimal:4',
            'remaining_qty' => 'decimal:4',

            'required_date' => 'date',            
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'Draft';

    public const STATUS_ASSIGNED = 'Assigned';

    public const STATUS_WAITING_APPROVAL = 'Waiting Approval';

    public const STATUS_APPROVED = 'Approved';

    public const STATUS_REJECTED = 'Rejected';

    public const STATUS_CANCELLED = 'Cancelled';

    /*
    |--------------------------------------------------------------------------
    | Approval Status
    |--------------------------------------------------------------------------
    */

    public const APPROVAL_PENDING = 'Pending';

    public const APPROVAL_APPROVED = 'Approved';

    public const APPROVAL_REJECTED = 'Rejected';

    /*
    |--------------------------------------------------------------------------
    | Workflow Options
    |--------------------------------------------------------------------------
    */

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT             => 'Draft',
            self::STATUS_ASSIGNED          => 'Assigned',
            self::STATUS_WAITING_APPROVAL  => 'Waiting Approval',
            self::STATUS_APPROVED          => 'Approved',
            self::STATUS_REJECTED          => 'Rejected',
            self::STATUS_CANCELLED         => 'Cancelled',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Approval Options
    |--------------------------------------------------------------------------
    */

    public static function getApprovalStatusOptions(): array
    {
        return [
            self::APPROVAL_PENDING   => 'Pending',
            self::APPROVAL_APPROVED  => 'Approved',
            self::APPROVAL_REJECTED  => 'Rejected',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Assignment Header.
     */
    public function assignmentMaterialRequisition(): BelongsTo
    {
        return $this->belongsTo(
            AssignmentMaterialRequisition::class,
            'assignment_material_requisition_id'
        );
    }

    /**
     * Purchase Requisition Item.
     */
    public function purchaseRequisitionItem(): BelongsTo
    {
        return $this->belongsTo(
            PurchaseRequisitionItem::class,
            'purchase_requisition_item_id'
        );
    }


    public function item(): BelongsTo
    {
        return $this->belongsTo(
            Item::class,
            'item_id'
        );
    }


    public function uom(): BelongsTo
    {
        return $this->belongsTo(
            Uom::class,
            'uom_id'
        );
    }    

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(
            Warehouse::class,
            'warehouse_id'
        );
    }


    /**
     * Selected Supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(
            Supplier::class,
            'supplier_id'
        );
    }

    /**
     * Tax Master.
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(
            TaxMaster::class,
            'tax_id'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_DRAFT
        );
    }

    public function scopeAssigned(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_ASSIGNED
        );
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_APPROVED
        );
    }

    public function scopeSelectedSupplier(Builder $query): Builder
    {
        return $query->where(
            'is_selected_supplier',
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Business Helper
    |--------------------------------------------------------------------------
    */

    public function hasQuotation(): bool
    {
        return $this->unit_price > 0;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isSelectedSupplier(): bool
    {
        return $this->is_selected_supplier;
    }

    /**
     * Subtotal setelah Discount.
     *
     * Nilai ini digunakan sebagai dasar
     * perhitungan pajak.
     */
    public function getSubTotal(): float
    {
        return max(
            0,
            (
                ($this->assigned_qty * $this->unit_price)
                - $this->discount_amount
            )
        );
    }

    /**
     * Grand Total.
     */
    public function getGrandTotal(): float
    {
        return (float) $this->grand_total;
    }

    /**
     * Gross Amount sebelum Discount.
     */
    public function getGrossAmount(): float
    {
        return (float) (
            $this->assigned_qty
            * $this->unit_price
        );
    }

    public function hasSupplier(): bool
    {
        return $this->supplier_id !== null;
    }

    public function hasWarehouse(): bool
    {
        return $this->warehouse_id !== null;
    }

    public function hasItem(): bool
    {
        return $this->item_id !== null;
    }    

    /*
    |--------------------------------------------------------------------------
    | Permission Helper
    |--------------------------------------------------------------------------
    */

    public function canEdit(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_DRAFT,
                self::STATUS_ASSIGNED,
            ],
            true
        );
    }

    public function canDelete(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    
}