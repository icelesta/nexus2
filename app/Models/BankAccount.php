<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    protected $table = 'bank_accounts';

    protected $fillable = [
        'uuid',

        'company_id',

        'bank_code',
        'bank_name',

        'account_no',
        'account_name',

        'currency_id',
        'coa_id',

        'branch_name',
        'swift_code',

        'opening_balance',

        'account_type',
        'payment_method',

        'allow_payment',
        'allow_receipt',
        'allow_transfer',

        'is_default',
        'is_active',

        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [

            'opening_balance' => 'decimal:2',

            'allow_payment'  => 'boolean',
            'allow_receipt'  => 'boolean',
            'allow_transfer' => 'boolean',

            'is_default' => 'boolean',
            'is_active'  => 'boolean',

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

    public function currency(): BelongsTo
    {
        return $this->belongsTo(
            Currency::class,
            'currency_id',
        );
    }

    public function coa(): BelongsTo
    {
        return $this->belongsTo(
            ChartOfAccount::class,
            'coa_id',
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by',
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by',
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

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return "{$this->bank_code} - {$this->bank_name}";
    }

    public function getFullAccountAttribute(): string
    {
        return "{$this->account_no} - {$this->account_name}";
    }
}