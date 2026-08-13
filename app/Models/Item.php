<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'items';

        /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute(): string
    {
        if (! empty($this->image)) {
            return asset('storage/' . $this->image);
        }

        return asset('images/no-image.png');
    }



        /*
    |--------------------------------------------------------------------------
    | Business Helper
    |--------------------------------------------------------------------------
    */

    public function isInventoryItem(): bool
    {
        return $this->is_inventory;
    }

    public function isPurchaseItem(): bool
    {
        return $this->is_purchase;
    }

    public function isSalesItem(): bool
    {
        return $this->is_sales;
    }

    public function isServiceItem(): bool
    {
        return $this->is_service;
    }

    public function isAssetItem(): bool
    {
        return $this->is_asset;
    }


    public function requiresSerialNumber(): bool
    {
        return $this->is_serialized;
    }

    public function requiresBatchNumber(): bool
    {
        return $this->is_batch_tracked;
    }

    public function requiresQualityInspection(): bool
    {
        return $this->quality_inspection_required;
    }


    public function hasDrawing(): bool
    {
        return filled($this->drawing_number);
    }

    public function hasPartNumber(): bool
    {
        return filled($this->part_number);
    }

    public function hasModelNumber(): bool
    {
        return filled($this->model_number);
    }


    public function hasBarcode(): bool
    {
        return filled($this->barcode);
    }

    public function hasQrCode(): bool
    {
        return filled($this->qr_code);
    }

    public function hasPreferredSupplier(): bool
    {
        return $this->suppliers()
            ->where('is_preferred', true)
            ->exists();
    }



    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    |
    | Only business fields are mass assignable.
    | System managed fields such as id, created_by, updated_by,
    | deleted_by and timestamps are handled by Observer / Laravel.
    |
    */

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | Identity
        |--------------------------------------------------------------------------
        */

        'uuid',
        'item_code',
        'item_name',
        'short_name',
        'search_name',
        'description',

        /*
        |--------------------------------------------------------------------------
        | Master Reference
        |--------------------------------------------------------------------------
        */

        'category_id',
        'brand_id',
        'manufacturer_id',

        'base_uom_id',
        'purchase_uom_id',
        'sales_uom_id',

        'warehouse_type_id',
        'default_warehouse_id',

        /*
        |--------------------------------------------------------------------------
        | Inventory Configuration
        |--------------------------------------------------------------------------
        */

        'item_type',
        'stock_type',

        'minimum_stock',
        'maximum_stock',
        'reorder_level',
        'reorder_quantity',
        'safety_stock',
        'economic_order_qty',

        'lead_time_days',

        'allow_negative_stock',
        'cycle_count_required',
        'quality_inspection_required',

        /*
        |--------------------------------------------------------------------------
        | Purchasing
        |--------------------------------------------------------------------------
        */

        'is_purchase',
        'is_subcontract',

        'default_purchase_lead_time',
        'purchase_tolerance',
        'minimum_purchase_qty',
        'purchase_requires_approval',

        'purchase_currency_id',

        /*
        |--------------------------------------------------------------------------
        | Sales
        |--------------------------------------------------------------------------
        */

        'is_sales',

        'allow_discount',
        'minimum_sales_qty',
        'sales_multiple',
        'allow_backorder',
        'requires_serial_sales',

        'sales_currency_id',

        /*
        |--------------------------------------------------------------------------
        | Accounting
        |--------------------------------------------------------------------------
        */

        'inventory_account_id',
        'expense_account_id',
        'income_account_id',

        /*
        |--------------------------------------------------------------------------
        | Engineering
        |--------------------------------------------------------------------------
        */

        'country_of_origin',
        'hs_code',

        'part_number',
        'model_number',
        'drawing_number',
        'unique_number',
        'revision_number',

        /*
        |--------------------------------------------------------------------------
        | Physical Specification
        |--------------------------------------------------------------------------
        */

        'net_weight',
        'gross_weight',

        'length',
        'width',
        'height',
        'volume',

        /*
        |--------------------------------------------------------------------------
        | Item Configuration
        |--------------------------------------------------------------------------
        */

        'is_inventory',
        'is_asset',
        'is_service',
        'is_manufacturing',
        'is_rental',

        'is_serialized',
        'is_batch_tracked',

        'is_quality_control',
        'is_returnable',
        'is_expirable',
        'is_hazardous',
        'is_consignment',
        'requires_certificate',

        'is_active',

        /*
        |--------------------------------------------------------------------------
        | Barcode & QR Code
        |--------------------------------------------------------------------------
        */

        'image',

        'barcode',
        'barcode_type',
        'barcode_printed',

        'qr_code',
        'qr_code_type',
        'qr_printed',

        /*
        |--------------------------------------------------------------------------
        | Remarks
        |--------------------------------------------------------------------------
        */

        'remarks',

    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'minimum_stock'      => 'decimal:4',
            'maximum_stock'      => 'decimal:4',
            'reorder_level'      => 'decimal:4',
            'reorder_quantity'   => 'decimal:4',
            'safety_stock'       => 'decimal:4',
            'economic_order_qty' => 'decimal:4',

            'net_weight'                => 'decimal:4',
            'gross_weight'              => 'decimal:4',
            'length'                    => 'decimal:4',
            'width'                     => 'decimal:4',
            'height'                    => 'decimal:4',
            'volume'                    => 'decimal:4',

            'purchase_tolerance'        => 'decimal:4',
            'minimum_purchase_qty'      => 'decimal:4',
            'minimum_sales_qty'         => 'decimal:4',
            'sales_multiple'            => 'decimal:4',

            'is_inventory'      => 'boolean',
            'is_purchase'       => 'boolean',
            'is_sales'          => 'boolean',
            'is_asset'          => 'boolean',
            'is_service'        => 'boolean',
            'is_serialized'     => 'boolean',
            'is_batch_tracked'  => 'boolean',
            'is_barcoded'       => 'boolean',
            'is_qrcode'         => 'boolean',
            'is_manufacturing'  => 'boolean',
            'is_rental'         => 'boolean',
            'is_active'         => 'boolean',
            //'image_uploaded_at' => 'datetime',
            'barcode_printed' => 'boolean',
            'qr_printed'                    => 'boolean',  
            'allow_negative_stock'          => 'boolean',
            'cycle_count_required'          => 'boolean',
            'quality_inspection_required'   => 'boolean',

            'is_subcontract'                => 'boolean',
            'purchase_requires_approval'    => 'boolean',

            'allow_discount'                => 'boolean',
            'allow_backorder'               => 'boolean',
            'requires_serial_sales'         => 'boolean',

            'is_quality_control'            => 'boolean',
            'is_returnable'                 => 'boolean',
            'is_expirable'                  => 'boolean',
            'is_hazardous'                  => 'boolean',
            'is_consignment'                => 'boolean',
            'requires_certificate'          => 'boolean',            

            'lead_time_days'               => 'integer',
            'default_purchase_lead_time'   => 'integer',

            'image' => 'string',

            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',

        ];
    }


    protected $with = [
        'category',
        'brand',
        'manufacturer',
        'baseUom',
    ];


    /*
    |--------------------------------------------------------------------------
    | Master Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Item Category
     */

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            ItemCategory::class,
            'category_id'
        );
    }

    /**
     * Item Brand
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(
            Brand::class,
            'brand_id'
        );
    }

    /**
     * Item Manufacturer
     */
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(
            Manufacturer::class,
            'manufacturer_id'
        );
    }

    /**
     * Base Unit of Measure
     */
    public function baseUom(): BelongsTo
    {
        return $this->belongsTo(
            Uom::class,
            'base_uom_id'
        );
    }

    /**
     * Purchase Unit of Measure
     */
    public function purchaseUom(): BelongsTo
    {
        return $this->belongsTo(
            Uom::class,
            'purchase_uom_id'
        );
    }

    /**
     * Sales Unit of Measure
     */
    public function salesUom(): BelongsTo
    {
        return $this->belongsTo(
            Uom::class,
            'sales_uom_id'
        );
    }

    /**
     * Warehouse Type
     */
    public function warehouseType(): BelongsTo
    {
        return $this->belongsTo(
            WarehouseType::class,
            'warehouse_type_id'
        );
    }

    /**
     * Default Warehouse
     */
    public function defaultWarehouse(): BelongsTo
    {
        return $this->belongsTo(
            Warehouse::class,
            'default_warehouse_id'
        );
    }

    /**
     * Backward Compatibility Alias
     *
     * @deprecated Use defaultWarehouse() instead.
     */
    public function warehouse(): BelongsTo
    {
        return $this->defaultWarehouse();
    }


    /*
    |--------------------------------------------------------------------------
    | Accounting Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Purchase Currency
     */
    public function purchaseCurrency(): BelongsTo
    {
        return $this->belongsTo(
            Currency::class,
            'purchase_currency_id'
        );
    }

    /**
     * Sales Currency
     */
    public function salesCurrency(): BelongsTo
    {
        return $this->belongsTo(
            Currency::class,
            'sales_currency_id'
        );
    }

    /**
     * Inventory Account
     */
    public function inventoryAccount(): BelongsTo
    {
        return $this->belongsTo(
            ChartOfAccount::class,
            'inventory_account_id'
        );
    }

    /**
     * Expense Account
     */
    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(
            ChartOfAccount::class,
            'expense_account_id'
        );
    }

    /**
     * Income Account
     */
    public function incomeAccount(): BelongsTo
    {
        return $this->belongsTo(
            ChartOfAccount::class,
            'income_account_id'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeInventory(Builder $query): Builder
    {
        return $query->where('is_inventory', true);
    }

    public function scopePurchase(Builder $query): Builder
    {
        return $query->where('is_purchase', true);
    }

    public function scopeSales(Builder $query): Builder
    {
        return $query->where('is_sales', true);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(
            ItemSupplier::class,
            'item_id'
        );
    }

    public function prices(): HasMany
    {
        return $this->hasMany(
            ItemPrice::class,
            'item_id'
        );
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(
            ItemStock::class,
            'item_id'
        );
    }


    public function images(): HasMany
    {
        return $this->hasMany(
            ItemImage::class,
            'item_id'
        );
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(
            ItemImage::class,
            'item_id'
        )->where('is_primary', true);
    }

    public function barcodes(): HasMany
    {
        return $this->hasMany(
            ItemBarcode::class,
            'item_id'
        );
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(
            ItemAttachment::class,
            'item_id'
        );
    }

    public function batches(): HasMany
    {
        return $this->hasMany(
            ItemBatch::class,
            'item_id'
        );
    }

    public function serials(): HasMany
    {
        return $this->hasMany(
            ItemSerial::class,
            'item_id'
        );
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(
            ItemSpecification::class,
            'item_id'
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

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'deleted_by'
        );
    } 

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeService(Builder $query): Builder
    {
        return $query->where('is_service', true);
    }

    public function scopeAsset(Builder $query): Builder
    {
        return $query->where('is_asset', true);
    }

    public function scopeManufacturing(Builder $query): Builder
    {
        return $query->where('is_manufacturing', true);
    }


    public function scopeRental(Builder $query): Builder
    {
        return $query->where('is_rental', true);
    }

    public function scopeSerialized(Builder $query): Builder
    {
        return $query->where('is_serialized', true);
    }

    public function scopeBatchTracked(Builder $query): Builder
    {
        return $query->where('is_batch_tracked', true);
    }

    public function scopeQualityControl(Builder $query): Builder
    {
        return $query->where('is_quality_control', true);
    }

    public function scopeReturnable(Builder $query): Builder
    {
        return $query->where('is_returnable', true);
    }

    public function scopeExpirable(Builder $query): Builder
    {
        return $query->where('is_expirable', true);
    }


}