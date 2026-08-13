<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    use SoftDeletes;

    /**
     * Table
     */
    protected $table = 'warehouses';

    /**
     * Mass Assignment
     */
    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | SYSTEM
        |--------------------------------------------------------------------------
        */

        'uuid',

        /*
        |--------------------------------------------------------------------------
        | COMPANY
        |--------------------------------------------------------------------------
        */

        'company_id',
        'branch_id',
        'warehouse_type_id',

        /*
        |--------------------------------------------------------------------------
        | GENERAL INFORMATION
        |--------------------------------------------------------------------------
        */

        'warehouse_code',
        'warehouse_name',
        'short_name',
        'description',

        /*
        |--------------------------------------------------------------------------
        | LOCATION
        |--------------------------------------------------------------------------
        */

        'address',
        'city',
        'province',
        'postal_code',
        'country',

        /*
        |--------------------------------------------------------------------------
        | CONTACT
        |--------------------------------------------------------------------------
        */

        'contact_person',
        'phone',
        'email',

        /*
        |--------------------------------------------------------------------------
        | BUSINESS RULES
        |--------------------------------------------------------------------------
        */

        'allow_purchase',
        'allow_sales',
        'allow_transfer',
        'allow_production',
        'allow_negative_stock',

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        'is_default',
        'is_active',
        'sort_order',

        /*
        |--------------------------------------------------------------------------
        | REMARKS
        |--------------------------------------------------------------------------
        */

        'remarks',

        /*
        |--------------------------------------------------------------------------
        | AUDIT
        |--------------------------------------------------------------------------
        */

        'created_by',
        'updated_by',
        'deleted_by',

    ];

    /**
     * Attribute Casting
     */
    protected function casts(): array
    {
        return [

            'allow_purchase'       => 'boolean',
            'allow_sales'          => 'boolean',
            'allow_transfer'       => 'boolean',
            'allow_production'     => 'boolean',
            'allow_negative_stock' => 'boolean',

            'is_default'           => 'boolean',
            'is_active'            => 'boolean',

            'sort_order'           => 'integer',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            'company_id'
        );
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(
            Branch::class,
            'branch_id'
        );
    }

    public function warehouseType(): BelongsTo
    {
        return $this->belongsTo(
            WarehouseType::class,
            'warehouse_type_id'
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
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return "{$this->warehouse_code} - {$this->warehouse_name}";
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    public function scopeCompany(
        Builder $query,
        int $companyId
    ): Builder {
        return $query->where(
            'company_id',
            $companyId
        );
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('warehouse_code');
    }
}