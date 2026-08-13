<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Currency;

class AssignmentMaterialRequisition extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'assignment_material_requisitions';


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
        | Purchase Requisition Reference
        |--------------------------------------------------------------------------
        */

        'purchase_requisition_id',

        /*
        |--------------------------------------------------------------------------
        | Assignment Information
        |--------------------------------------------------------------------------
        */

        'assigned_to',
        'assigned_by',
        'assigned_at',
        'pr_number',

        /*
        |--------------------------------------------------------------------------
        | Snapshot Header
        |--------------------------------------------------------------------------
        */

        'company_id',
        'business_unit_id',
        'branch_id',
        'department_id',
        'section_id',
        'cost_center_id',
        'warehouse_id',

        'shipping_address_id',

        'currency_id',
        'exchange_rate',        

        'requester_id',

        'request_date',
        'required_date',

        'priority',

        'reference_no',
        'delivery_location',

        /*
        |--------------------------------------------------------------------------
        | Workflow
        |--------------------------------------------------------------------------
        */

        'status',

        'created_by',
        'updated_by',
        'deleted_by',        

        /*
        |--------------------------------------------------------------------------
        | Remarks
        |--------------------------------------------------------------------------
        */

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

            'document_date' => 'date',
            'assigned_at'   => 'datetime',

            'request_date'  => 'date',
            'required_date' => 'date',

            'exchange_rate' => 'decimal:6',

            'created_at'    => 'datetime',
            'updated_at'    => 'datetime',
            'deleted_at'    => 'datetime',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'Draft';

    public const STATUS_ASSIGNED = 'Assigned';

    public const STATUS_WAITING_APPROVAL = 'Waiting Approval';

    public const STATUS_APPROVED = 'Approved';

    public const STATUS_COMPLETED = 'Completed';

    public const STATUS_REJECTED = 'Rejected';

    public const STATUS_CANCELLED = 'Cancelled';

    /*
    |--------------------------------------------------------------------------
    | Status Options
    |--------------------------------------------------------------------------
    */

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT              => 'Draft',
            self::STATUS_ASSIGNED           => 'Assigned',
            self::STATUS_WAITING_APPROVAL   => 'Waiting Approval',
            self::STATUS_APPROVED           => 'Approved',
            self::STATUS_COMPLETED          => 'Completed',
            self::STATUS_REJECTED           => 'Rejected',
            self::STATUS_CANCELLED          => 'Cancelled',
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
     * Requester Snapshot.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'requester_id'
        );
    }


    /**
     * Assigned Buyer.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_to'
        );
    }

    /**
     * Assigned By.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_by'
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
     * Assignment Detail Items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(
            AssignmentMaterialRequisitionItem::class,
            'assignment_material_requisition_id'
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

    public function scopeWaitingApproval(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_WAITING_APPROVAL
        );
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_APPROVED
        );
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_COMPLETED
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

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    public function isWaitingApproval(): bool
    {
        return $this->status === self::STATUS_WAITING_APPROVAL;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Helpers
    |--------------------------------------------------------------------------
    */

    public function canEdit(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_ASSIGNED,
        ], true);
    }

    public function canDelete(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_DRAFT,
                self::STATUS_ASSIGNED,
            ],
            true,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether document can be submitted.
     */
    public function canSubmit(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Determine whether document can be approved.
     */
    public function canApprove(): bool
    {
        return $this->status === self::STATUS_WAITING_APPROVAL;
    }

    /**
     * Determine whether document can be rejected.
     */
    public function canReject(): bool
    {
        return $this->status === self::STATUS_WAITING_APPROVAL;
    }

    /**
     * Determine whether document can be completed.
     */
    public function canComplete(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Determine whether document can be cancelled.
     */
    public function canCancel(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_ASSIGNED,
            self::STATUS_WAITING_APPROVAL,
        ], true);
    }

    /**
     * Determine whether document is locked.
     */
    public function isLocked(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_APPROVED,
                self::STATUS_COMPLETED,
                self::STATUS_REJECTED,
                self::STATUS_CANCELLED,
            ],
            true,
        );
    } 

    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(
            PurchaseOrder::class,
            'assignment_material_requisition_id'
        );
    }  

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    /**
     * Currency used for this Assignment.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(
            Currency::class,
            'currency_id'
        );
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(
            ShippingAddress::class,
            'shipping_address_id'
        );
    }
    
}