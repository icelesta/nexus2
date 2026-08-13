<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $table = 'chart_of_accounts';

    protected $fillable = [

        'uuid',

        'company_id',

        'account_code',

        'account_name',

        'parent_account_id',

        'account_type',

        'normal_balance',

        'currency_id',

        'is_control_account',

        'allow_manual_entry',

        'is_cash_account',

        'is_bank_account',

        'is_tax_account',

        'is_retained_earning',

        'is_active',

        'sort_order',

        'description',

        'created_by',

        'updated_by',

        'deleted_by',

    ];

    protected function casts(): array
    {
        return [

            'is_control_account' => 'boolean',

            'allow_manual_entry' => 'boolean',

            'is_cash_account' => 'boolean',

            'is_bank_account' => 'boolean',

            'is_tax_account' => 'boolean',

            'is_retained_earning' => 'boolean',

            'is_active' => 'boolean',

            'sort_order' => 'integer',

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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_account_id'
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_account_id'
        );
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(
            Currency::class
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
    | Query Scope
    |--------------------------------------------------------------------------
    */

    public function scopeActive(
        Builder $query
    ): Builder
    {
        return $query->where(
            'is_active',
            true
        );
    }

    public function scopeManualEntry(
        Builder $query
    ): Builder
    {
        return $query->where(
            'allow_manual_entry',
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return sprintf(
            '%s - %s',
            $this->account_code,
            $this->account_name
        );
    }
}