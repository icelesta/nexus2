<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Uom extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Table
     */
    protected $table = 'uoms';

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
        | UOM Information
        |--------------------------------------------------------------------------
        */

        'uom_code',
        'uom_name',
        'symbol',
        'category',

        /*
        |--------------------------------------------------------------------------
        | Configuration
        |--------------------------------------------------------------------------
        */

        'decimal_places',
        'allow_fraction',
        'is_base',
        'sort_order',

        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        'remarks',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'is_active',

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        'created_by',
        'updated_by',
    ];

    /**
     * Attribute Casting
     */
    protected function casts(): array
    {
        return [

            'decimal_places' => 'integer',

            'allow_fraction' => 'boolean',
            'is_base'        => 'boolean',
            'is_active'      => 'boolean',

            'sort_order'     => 'integer',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

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
        return "{$this->uom_code} - {$this->uom_name}";
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

    public function scopeBase(Builder $query): Builder
    {
        return $query->where(
            'is_base',
            true
        );
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('uom_name');
    }
}