<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExchangeRate extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'uuid',

        'from_currency_id',
        'to_currency_id',

        'exchange_date',

        'rate_type',

        'buy_rate',
        'sell_rate',
        'middle_rate',

        'source',
        'remarks',

        'is_active',

        'created_by',
        'updated_by',
        'deleted_by',

    ];

    protected function casts(): array
    {
        return [

            'exchange_date' => 'date',

            'buy_rate' => 'decimal:8',
            'sell_rate' => 'decimal:8',
            'middle_rate' => 'decimal:8',

            'is_active' => 'boolean',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(
            Currency::class,
            'from_currency_id'
        );
    }

    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(
            Currency::class,
            'to_currency_id'
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
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->orderByDesc('exchange_date');
    }

    public function scopeSpot(Builder $query): Builder
    {
        return $query->where('rate_type', 'Spot');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return sprintf(
            '%s → %s',
            $this->fromCurrency?->currency_code ?? '-',
            $this->toCurrency?->currency_code ?? '-'
        );
    }

    public function getCurrencyPairAttribute(): string
    {
        return sprintf(
            '%s/%s',
            $this->fromCurrency?->currency_code ?? '-',
            $this->toCurrency?->currency_code ?? '-'
        );
    }
}