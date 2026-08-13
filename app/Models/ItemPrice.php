<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Number;

class ItemPrice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'item_prices';

    protected $fillable = [];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'uuid' => 'string',

            'price' => 'decimal:4',

            'effective_date' => 'date',

            'expiry_date' => 'date',

            'is_default' => 'boolean',

            'is_active' => 'boolean',

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

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(
            ItemSupplier::class,
            'item_supplier_id'
        );
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(
            Currency::class,
            'currency_id'
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

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayPriceAttribute(): string
    {
        return trim(sprintf(
            '%s %s',
            $this->currency?->currency_code ?? '',
            Number::format(
                (float) $this->price,
                4
            )
        ));
    }

    public function getDisplayNameAttribute(): string
    {
        return sprintf(
            '%s - %s',
            $this->price_type,
            $this->display_price
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isEffective(): bool
    {
        return today()->greaterThanOrEqualTo(
            $this->effective_date
        );
    }

    public function isExpired(): bool
    {
        return filled($this->expiry_date)
            && today()->greaterThan(
                $this->expiry_date
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'is_active',
            true
        );
    }

    public function scopeDefault(
        Builder $query
    ): Builder {
        return $query->where(
            'is_default',
            true
        );
    }

    public function scopeEffective(
        Builder $query
    ): Builder {

        return $query

            ->whereDate(
                'effective_date',
                '<=',
                today()
            )

            ->where(function (
                Builder $query
            ) {

                $query

                    ->whereNull(
                        'expiry_date'
                    )

                    ->orWhereDate(
                        'expiry_date',
                        '>=',
                        today()
                    );

            });
    }

    public function scopeValid(
        Builder $query
    ): Builder {

        return $query

            ->active()

            ->effective();
    }

    public function scopePurchase(
        Builder $query
    ): Builder {

        return $query->where(
            'price_type',
            'Purchase'
        );
    }

    public function scopeSales(
        Builder $query
    ): Builder {

        return $query->where(
            'price_type',
            'Sales'
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

        return $query

            ->orderBy(
                'price_type'
            )

            ->orderByDesc(
                'effective_date'
            );
    }
}