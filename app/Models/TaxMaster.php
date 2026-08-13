<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxMaster extends Model
{
    use SoftDeletes;

    protected $table = 'tax_masters';

    protected $fillable = [

        'uuid',

        'company_id',

        'tax_code',
        'tax_name',

        'tax_type',

        'tax_rate',

        'calculation_method',

        'tax_account_id',

        'effective_date',
        'expired_date',

        'is_default',
        'is_inclusive',
        'is_withholding',

        'sort_order',

        'remarks',

        'is_active',

        'created_by',
        'updated_by',

    ];

    protected function casts(): array
    {
        return [

            'tax_rate' => 'decimal:4',

            'effective_date' => 'date',

            'expired_date' => 'date',

            'is_default' => 'boolean',

            'is_inclusive' => 'boolean',

            'is_withholding' => 'boolean',

            'is_active' => 'boolean',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class
        );
    }

    public function taxAccount(): BelongsTo
    {
        return $this->belongsTo(
            ChartOfAccount::class,
            'tax_account_id'
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

    public function scopeType(
        Builder $query,
        string $type
    ): Builder {

        return $query->where(
            'tax_type',
            $type
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return "{$this->tax_code} - {$this->tax_name}";
    }

    public function getTaxDescriptionAttribute(): string
    {
        return sprintf(
            '%s (%.2f%%)',
            $this->tax_name,
            $this->tax_rate
        );
    }
}