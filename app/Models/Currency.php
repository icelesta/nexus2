<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Currency extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',

        'currency_code',
        'currency_name',

        'symbol',

        'decimal_places',

        'is_base_currency',
        'is_active',

        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'decimal_places'  => 'integer',
            'is_base_currency' => 'boolean',
            'is_active'        => 'boolean',
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
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeBaseCurrency(Builder $query): Builder
    {
        return $query->where('is_base_currency', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return "{$this->currency_code} - {$this->currency_name}";
    }
}