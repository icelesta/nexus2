<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemSupplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Table
     */
    protected $table = 'item_suppliers';

    /**
     * Mass Assignment
     */
    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        'uuid',

        /*
        |--------------------------------------------------------------------------
        | Relationship
        |--------------------------------------------------------------------------
        */

        'item_id',
        'supplier_id',
        'purchase_uom_id',

        /*
        |--------------------------------------------------------------------------
        | Supplier Information
        |--------------------------------------------------------------------------
        */

        'supplier_item_code',
        'supplier_item_name',

        'purchase_description',

        /*
        |--------------------------------------------------------------------------
        | Purchasing
        |--------------------------------------------------------------------------
        */

        'is_preferred',

        'supplier_priority',

        'lead_time_days',

        'minimum_order_qty',

        'purchase_multiple',

        'default_tax_id',

        'default_discount',

        'last_purchase_price',

        'last_purchase_date',

        /*
        |--------------------------------------------------------------------------
        | Additional Information
        |--------------------------------------------------------------------------
        */

        'remarks',

        'is_active',

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        'created_by',

        'updated_by',
    ];

/*    protected $with = [

        'category',

        'brand',

        'manufacturer',

        'baseUom',

    ]; */

    /**
     * Attribute Casting
     */
    protected function casts(): array
    {
        return [

            'is_preferred' => 'boolean',

            'is_active'    => 'boolean',

            'lead_time_days' => 'integer',

            'supplier_priority' => 'integer',

            'minimum_order_qty' => 'decimal:4',

            'purchase_multiple' => 'decimal:4',

            'default_discount' => 'decimal:2',

            'last_purchase_price' => 'decimal:4',

            'last_purchase_date'  => 'date',

            'uuid' => 'string',

            'created_at'=>'datetime',

            'updated_at'=>'datetime',

            'deleted_at'=>'datetime',

        ];
    }

    public function scopeCurrent(
        Builder $query,
    ): Builder {

        return $query
            ->active()
            ->ordered();
    }    

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function item(): BelongsTo
    {
        return $this->belongsTo(
            Item::class
        );
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(
            Supplier::class
        );
    }

    public function purchaseUom(): BelongsTo
    {
        return $this->belongsTo(
            Uom::class,
            'purchase_uom_id'
        );
    }

    public function defaultTax(): BelongsTo
    {
        return $this->belongsTo(
            TaxMaster::class,
            'default_tax_id'
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

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        $supplier = $this->supplier?->supplier_name ?? '-';

        $code = $this->supplier_item_code ?: '-';

        return "{$supplier} ({$code})";
    }

    public function getLastPurchasePriceFormattedAttribute(): string
    {
        return number_format(
            (float) ($this->last_purchase_price ?? 0),
            2
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'is_active',
            true
        );
    }

    public function scopePreferred(Builder $query): Builder
    {
        return $query->where(
            'is_preferred',
            true
        );
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('supplier_priority')
            ->orderBy('supplier_item_name');
    }

    public function scopeSupplier(
        Builder $query,
        int $supplierId,
    ): Builder {

        return $query->where(
            'supplier_id',
            $supplierId
        );
    }

    public function scopeItem(
        Builder $query,
        int $itemId,
    ): Builder {

        return $query->where(
            'item_id',
            $itemId
        );
    }    

    public function hasPurchaseHistory(): bool
    {
        return ! is_null(
            $this->last_purchase_date
        );
    }

}