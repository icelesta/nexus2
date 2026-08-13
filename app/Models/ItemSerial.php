<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemSerial extends Model
{
    use HasFactory;

    protected $table = 'item_serials';

    protected $guarded = [];

    /**
     * Table does not have created_at / updated_at columns.
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
            'status' => 'string',
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

    public function getDisplayNameAttribute(): string
    {
        return sprintf(
            '%s (%s)',
            $this->serial_number,
            $this->status ?? 'Unknown'
        );
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
            'status',
            'Available'
        );
    }

    public function scopeReserved(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'Reserved'
        );
    }

    public function scopeSold(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'Sold'
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
}