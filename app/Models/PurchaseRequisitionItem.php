<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class PurchaseRequisitionItem extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_requisition_items';

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'Draft';
    public const STATUS_SUBMITTED = 'Submitted';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_REJECTED = 'Rejected';
    public const STATUS_CANCELLED = 'Cancelled';
    public const STATUS_CLOSED = 'Closed';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'uuid',

        'purchase_requisition_id',

        'item_id',
        'uom_id',
        'warehouse_id',

        'quantity',
        'estimated_unit_price',

        'required_date',
        'remarks',

        'status',

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
            'purchase_requisition_id' => 'integer',
            'item_id' => 'integer',
            'uom_id' => 'integer',
            'warehouse_id' => 'integer',

            'quantity' => 'decimal:4',
            'estimated_unit_price' => 'decimal:2',

            'required_date' => 'date',

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

    public function purchaseRequisition(): BelongsTo
    {
        return $this->belongsTo(
            PurchaseRequisition::class
        );
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(
            Item::class
        );
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(
            Uom::class
        );
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(
            Warehouse::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | AMR Commercial Synchronization
    |--------------------------------------------------------------------------
    */

    public function assignmentMaterialRequisitionItems(): HasMany
    {
        return $this->hasMany(
            AssignmentMaterialRequisitionItem::class,
            'purchase_requisition_item_id'
        );
    }

    public function latestAssignmentMaterialRequisitionItem(): HasOne
    {
        return $this->hasOne(
            AssignmentMaterialRequisitionItem::class,
            'purchase_requisition_item_id'
        )->latestOfMany('id');
    }


    /*
    |--------------------------------------------------------------------------
    | Audit Relationships
    |--------------------------------------------------------------------------
    */

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'deleted_by'
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

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_SUBMITTED
        );
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_APPROVED
        );
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_REJECTED
        );
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_CANCELLED
        );
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_CLOSED
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    public function getEstimatedTotalAttribute(): float
    {
        return (float) $this->quantity
            * (float) $this->estimated_unit_price;
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }
}