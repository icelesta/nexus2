<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseType extends Model
{
    use SoftDeletes;

    protected $table = 'warehouse_types';

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | System
        |--------------------------------------------------------------------------
        */

        'uuid',

        /*
        |--------------------------------------------------------------------------
        | Organization
        |--------------------------------------------------------------------------
        */

        'company_id',

        /*
        |--------------------------------------------------------------------------
        | General
        |--------------------------------------------------------------------------
        */

        'type_code',
        'type_name',
        'description',

        /*
        |--------------------------------------------------------------------------
        | Business Rules
        |--------------------------------------------------------------------------
        */

        'allow_purchase',
        'allow_sales',
        'allow_transfer',
        'allow_production',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'sort_order',
        'is_default',
        'is_active',

        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        'created_by',
        'updated_by',

    ];

    protected function casts(): array
    {
        return [

            'allow_purchase'   => 'boolean',
            'allow_sales'      => 'boolean',
            'allow_transfer'   => 'boolean',
            'allow_production' => 'boolean',

            'is_default'       => 'boolean',
            'is_active'        => 'boolean',

            'sort_order'       => 'integer',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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

    public function warehouses(): HasMany
    {
        return $this->hasMany(
            Warehouse::class,
            'warehouse_type_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return "{$this->type_code} - {$this->type_name}";
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
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
            ->orderBy('type_name');
    }

    /*
    |--------------------------------------------------------------------------
    | Business Rules
    |--------------------------------------------------------------------------
    */

    public function canPurchase(): bool
    {
        return $this->allow_purchase;
    }

    public function canSales(): bool
    {
        return $this->allow_sales;
    }

    public function canTransfer(): bool
    {
        return $this->allow_transfer;
    }

    public function canProduction(): bool
    {
        return $this->allow_production;
    }
}