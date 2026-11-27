<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\GoodsReceiptItem;
use App\Models\AssignmentDirectMarket;

use App\Models\GoodsReceiptAudit;

class GoodsReceipt extends Model
{
    use SoftDeletes;

    protected $table = 'goods_receipts';

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'Draft';
    public const STATUS_SUBMITTED = 'Submitted';
    public const STATUS_RECEIVED = 'Received';
    public const STATUS_PARTIALLY_RECEIVED = 'Partially Received';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_CANCELLED = 'Cancelled';
    public const STATUS_CLOSED = 'Closed';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'uuid',
        'grn_no',
        'document_number',

        'purchase_order_id',
        'assignment_direct_market_id',
        'supplier_id',
        'warehouse_id',

        'receipt_date',

        'status',
        'items_status',
        'rr_status',
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
            'purchase_order_id' => 'integer',
            'assignment_direct_market_id' => 'integer',
            'supplier_id' => 'integer',
            'warehouse_id' => 'integer',

            'receipt_date' => 'date',

            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',

            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(
            GoodsReceiptItem::class,
            'goods_receipt_id'
        );
    }

    public function assignmentDirectMarket(): BelongsTo
    {
        return $this->belongsTo(
            AssignmentDirectMarket::class,
            'assignment_direct_market_id'
        );
    }

    public function audits(): HasMany
    {
        return $this->hasMany(
            GoodsReceiptAudit::class,
            'goods_receipt_id'
        )->orderBy('created_at');
    }

}
