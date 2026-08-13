<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemStock extends Model
{
    use HasFactory;

    protected $table = 'item_stocks';

    protected $guarded = [];

    /**
     * The table does not contain Laravel timestamps.
     */
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'qty_on_hand'   => 'decimal:4',
            'qty_reserved'  => 'decimal:4',
            'qty_available' => 'decimal:4',
            'last_stock_take' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function item(): BelongsTo
    {
        return $this->belongsTo(
            Item::class,
            'item_id'
        );
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(
            Warehouse::class,
            'warehouse_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getHasStockAttribute(): bool
    {
        return (float) $this->qty_on_hand > 0;
    }

    public function getDisplayStockAttribute(): string
    {
        return number_format((float) $this->qty_available, 4);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function hasAvailableStock(): bool
    {
        return (float) $this->qty_available > 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAvailable(
        Builder $query
    ): Builder {
        return $query->where(
            'qty_available',
            '>',
            0
        );
    }

    public function scopeWarehouse(
        Builder $query,
        int $warehouseId
    ): Builder {
        return $query->where(
            'warehouse_id',
            $warehouseId
        );
    }

    public function scopeItem(
        Builder $query,
        int $itemId
    ): Builder {
        return $query->where(
            'item_id',
            $itemId
        );
    }

    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query->orderBy(
            'warehouse_id'
        );
    }
}