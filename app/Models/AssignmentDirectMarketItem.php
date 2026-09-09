<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignmentDirectMarketItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'assignment_direct_market_items';

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'Draft';

    public const STATUS_SUBMITTED = 'Submitted';

    public const STATUS_UPDATED = 'Updated';

    public const STATUS_WAITING_APPROVAL = 'Waiting Approval';

    public const STATUS_APPROVED = 'Approved';

    public const STATUS_REJECTED = 'Rejected';

    public const STATUS_CANCELLED = 'Cancelled';

    public const STATUS_COMPLETED = 'Completed';

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
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'assignment_direct_market_id',
        'direct_market_item_id',

        'item_id',
        'item_code',
        'item_name',

        'item_description',
        'specification',

        'uom_id',
        'uom_code',
        'uom_name',

        'requested_qty',
        'assigned_qty',

        'required_date',
        'delivery_location',

        'supplier_id',

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

        'gross_amount',
        'net_amount',
        'line_total',
        'grand_total',

        'is_selected_supplier',

        'buyer_notes',
        'remarks',

        'status',
        'approval_status',

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

            'assignment_direct_market_id' => 'integer',
            'direct_market_item_id' => 'integer',

            'item_id' => 'integer',
            'uom_id' => 'integer',

            'requested_qty' => 'decimal:4',
            'assigned_qty' => 'decimal:4',

            'required_date' => 'date',

            'supplier_id' => 'integer',

            'unit_price' => 'decimal:2',

            'quotation_date' => 'date',

            'lead_time_days' => 'integer',

            'delivery_date' => 'date',

            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',

            'tax_id' => 'integer',
            'tax_percent' => 'decimal:2',
            'tax_amount' => 'decimal:2',

            'gross_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'line_total' => 'decimal:2',
            'grand_total' => 'decimal:2',

            'is_selected_supplier' => 'boolean',

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

    public function assignmentDirectMarket(): BelongsTo
    {
        return $this->belongsTo(
            AssignmentDirectMarket::class,
            'assignment_direct_market_id'
        );
    }

    public function directMarketItem(): BelongsTo
    {
        return $this->belongsTo(
            DirectMarketItem::class,
            'direct_market_item_id'
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(
            Supplier::class,
            'supplier_id'
        );
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(
            TaxMaster::class,
            'tax_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater(): BelongsTo
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
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_DRAFT
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

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isWaitingApproval(): bool
    {
        return $this->status === self::STATUS_WAITING_APPROVAL;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
