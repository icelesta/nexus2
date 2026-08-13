<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'purchase_orders';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | Document Information
        |--------------------------------------------------------------------------
        */

        'document_no',
        'document_date',

        /*
        |--------------------------------------------------------------------------
        | Source Document
        |--------------------------------------------------------------------------
        */

        'purchase_requisition_id',
        'assignment_material_requisition_id',

        'pr_number',
        'amr_number',

        /*
        |--------------------------------------------------------------------------
        | Supplier
        |--------------------------------------------------------------------------
        */

        'supplier_id',

        'payment_term_id',

        /*
        |--------------------------------------------------------------------------
        | Organization Snapshot
        |--------------------------------------------------------------------------
        */

        'company_id',
        'business_unit_id',
        'branch_id',
        'department_id',
        'section_id',
        'cost_center_id',
        'warehouse_id',

        /*
        |--------------------------------------------------------------------------
        | Request Information
        |--------------------------------------------------------------------------
        */

        'requester_id',

        'request_date',
        'required_date',
        'expected_delivery_date',

        'priority',

        'reference_no',

        'shipping_address_id',

        /*
        |--------------------------------------------------------------------------
        | Financial
        |--------------------------------------------------------------------------
        */

        'currency_id',
        'exchange_rate',

        'subtotal',
        'discount_amount',
        'tax_amount',
        'grand_total',

        /*
        |--------------------------------------------------------------------------
        | Approval
        |--------------------------------------------------------------------------
        */

        'approval_status',

        'approved_by',
        'approved_at',

        /*
        |--------------------------------------------------------------------------
        | Generated Information
        |--------------------------------------------------------------------------
        */

        'generated_by',
        'generated_at',

        /*
        |--------------------------------------------------------------------------
        | Workflow
        |--------------------------------------------------------------------------
        */

        'status',

        /*
        |--------------------------------------------------------------------------
        | Remarks
        |--------------------------------------------------------------------------
        */

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
            | Document
            |--------------------------------------------------------------------------
            */

            'document_date'            => 'date',

            /*
            |--------------------------------------------------------------------------
            | Request Information
            |--------------------------------------------------------------------------
            */

            'request_date'             => 'date',
            'required_date'            => 'date',
            'expected_delivery_date'   => 'date',

            /*
            |--------------------------------------------------------------------------
            | Financial
            |--------------------------------------------------------------------------
            */

            'exchange_rate'            => 'decimal:6',

            'subtotal'                 => 'decimal:2',
            'discount_amount'          => 'decimal:2',
            'tax_amount'               => 'decimal:2',
            'grand_total'              => 'decimal:2',

            /*
            |--------------------------------------------------------------------------
            | Approval
            |--------------------------------------------------------------------------
            */

            'approved_at'              => 'datetime',

            /*
            |--------------------------------------------------------------------------
            | Generated
            |--------------------------------------------------------------------------
            */

            'generated_at'             => 'datetime',

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            'created_at'               => 'datetime',
            'updated_at'               => 'datetime',
            'deleted_at'               => 'datetime',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Document Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'Draft';

    public const STATUS_APPROVED = 'Approved';

    public const STATUS_PARTIALLY_RECEIVED = 'Partially Received';

    public const STATUS_COMPLETED = 'Completed';

    public const STATUS_CLOSED = 'Closed';

    public const STATUS_CANCELLED = 'Cancelled';


    /*
    |--------------------------------------------------------------------------
    | Approval Status
    |--------------------------------------------------------------------------
    */

    public const APPROVAL_PENDING = 'Pending';

    public const APPROVAL_WAITING = 'Waiting Approval';

    public const APPROVAL_APPROVED = 'Approved';

    public const APPROVAL_REJECTED = 'Rejected';

    /*
    |--------------------------------------------------------------------------
    | Document Status Options
    |--------------------------------------------------------------------------
    */

    public static function getStatusOptions(): array
    {
        return [

            self::STATUS_DRAFT                => 'Draft',

            self::STATUS_APPROVED             => 'Approved',

            self::STATUS_PARTIALLY_RECEIVED   => 'Partially Received',

            self::STATUS_COMPLETED            => 'Completed',

            self::STATUS_CLOSED               => 'Closed',

            self::STATUS_CANCELLED            => 'Cancelled',

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Approval Status Options
    |--------------------------------------------------------------------------
    */

    public static function getApprovalStatusOptions(): array
    {
        return [

            self::APPROVAL_PENDING    => 'Pending',

            self::APPROVAL_WAITING    => 'Waiting Approval',

            self::APPROVAL_APPROVED   => 'Approved',

            self::APPROVAL_REJECTED   => 'Rejected',

        ];
    }



    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Material Requisition Header.
     */
    public function purchaseRequisition(): BelongsTo
    {
        return $this->belongsTo(
            PurchaseRequisition::class,
            'purchase_requisition_id'
        );
    }

    /**
     * Assignment Material Requisition Header.
     */
    public function assignmentMaterialRequisition(): BelongsTo
    {
        return $this->belongsTo(
            AssignmentMaterialRequisition::class,
            'assignment_material_requisition_id'
        );
    }

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


    /**
     * Payment Terms.
     */
    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(
            PaymentTerm::class,
            'payment_term_id'
        );
    }

    /**
     * Currency.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(
            Currency::class,
            'currency_id'
        );
    }

    /**
     * Company Snapshot.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            'company_id'
        );
    }


    /**
     * Business Unit Snapshot.
     */
    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(
            BusinessUnit::class,
            'business_unit_id'
        );
    }

    /**
     * Branch Snapshot.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(
            Branch::class,
            'branch_id'
        );
    }

    /**
     * Department Snapshot.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(
            Department::class,
            'department_id'
        );
    }

    /**
     * Section Snapshot.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(
            Section::class,
            'section_id'
        );
    }

    /**
     * Cost Center Snapshot.
     */
    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(
            CostCenter::class,
            'cost_center_id'
        );
    }

    /**
     * Warehouse Snapshot.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(
            Warehouse::class,
            'warehouse_id'
        );
    }

    /**
     * Requester.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'requester_id'
        );
    }


    /**
     * Approved By.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }


    /**
     * Generated By.
     */
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'generated_by'
        );
    }

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

    /**
     * Purchase Order Detail Items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(
            PurchaseOrderItem::class,
            'purchase_order_id'
        );
    }

 
    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Draft Purchase Orders.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Approved Purchase Orders.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Partially Received Purchase Orders.
     */
    public function scopePartiallyReceived(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_PARTIALLY_RECEIVED
        );
    }

    /**
     * Completed Purchase Orders.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_COMPLETED
        );
    }

    /**
     * Closed Purchase Orders.
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_CLOSED
        );
    }

    /**
     * Cancelled Purchase Orders.
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
    | Approval Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Pending Approval.
     */
    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->where(
            'approval_status',
            self::APPROVAL_PENDING
        );
    }

    /**
     * Waiting Approval.
     */
    public function scopeWaitingApproval(Builder $query): Builder
    {
        return $query->where(
            'approval_status',
            self::APPROVAL_WAITING
        );
    }

    /**
     * Approved Approval.
     */
    public function scopeApprovalApproved(Builder $query): Builder
    {
        return $query->where(
            'approval_status',
            self::APPROVAL_APPROVED
        );
    }

    /**
     * Rejected Approval.
     */
    public function scopeRejectedApproval(Builder $query): Builder
    {
        return $query->where(
            'approval_status',
            self::APPROVAL_REJECTED
        );
    }


    /*
    |--------------------------------------------------------------------------
    | State Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check whether the Purchase Order is Draft.
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check whether the Purchase Order is Approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check whether the Purchase Order is Partially Received.
     */
    public function isPartiallyReceived(): bool
    {
        return $this->status === self::STATUS_PARTIALLY_RECEIVED;
    }

    /**
     * Check whether the Purchase Order is Completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check whether the Purchase Order is Closed.
     */
    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * Check whether the Purchase Order is Cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }


    /*
    |--------------------------------------------------------------------------
    | Approval Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check whether approval is pending.
     */
    public function isPendingApproval(): bool
    {
        return $this->approval_status === self::APPROVAL_PENDING;
    }

    /**
     * Check whether approval is waiting.
     */
    public function isWaitingApproval(): bool
    {
        return $this->approval_status === self::APPROVAL_WAITING;
    }

    /**
     * Check whether approval is approved.
     */
    public function isApprovalApproved(): bool
    {
        return $this->approval_status === self::APPROVAL_APPROVED;
    }

    /**
     * Check whether approval is rejected.
     */
    public function isApprovalRejected(): bool
    {
        return $this->approval_status === self::APPROVAL_REJECTED;
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the document can be edited.
     */
    public function canEdit(): bool
    {
        return $this->isDraft();
    }

    /**
     * Determine whether the document can be deleted.
     */
    public function canDelete(): bool
    {
        return $this->isDraft();
    }

    /**
     * Determine whether the document can be submitted for approval.
     */
    public function canSubmit(): bool
    {
        return $this->isDraft();
    }

    /**
     * Determine whether the document can be approved.
     */
    public function canApprove(): bool
    {
        return $this->isWaitingApproval();
    }

    /**
     * Determine whether items can be received.
     */
    public function canReceive(): bool
    {
        return $this->isApproved()
            || $this->isPartiallyReceived();
    }

    /**
     * Determine whether the document can be closed.
     */
    public function canClose(): bool
    {
        return $this->isCompleted();
    }

    /**
     * Determine whether the document can be cancelled.
     */
    public function canCancel(): bool
    {
        return ! $this->isCancelled()
            && ! $this->isClosed();
    }


    /*
    |--------------------------------------------------------------------------
    | Lock Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the document is locked.
     */
    public function isLocked(): bool
    {
        return ! $this->isDraft();
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(
            ShippingAddress::class,
            'shipping_address_id'
        );
    }


}