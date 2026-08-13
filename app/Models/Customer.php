<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | General Information
        |--------------------------------------------------------------------------
        */

        'customer_code',
        'customer_name',
        'category_id',
        'customer_type',
        'tax_number',

        /*
        |--------------------------------------------------------------------------
        | Contact Information
        |--------------------------------------------------------------------------
        */

        'email',
        'phone',
        'mobile',
        'website',

        'pic_name',
        'pic_position',

        /*
        |--------------------------------------------------------------------------
        | Address Information
        |--------------------------------------------------------------------------
        */

        'address',
        'city',
        'province',
        'country',
        'postal_code',

        /*
        |--------------------------------------------------------------------------
        | Finance Information
        |--------------------------------------------------------------------------
        */

        'currency_id',
        'ar_account_id',
        'default_payment_term_id',

        /*
        |--------------------------------------------------------------------------
        | Credit Control
        |--------------------------------------------------------------------------
        */

        'credit_limit',
        'credit_days',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'is_preferred',
        'is_blacklisted',
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

            'credit_limit' => 'decimal:2',

            'credit_days' => 'integer',

            'is_preferred' => 'boolean',
            'is_blacklisted' => 'boolean',
            'is_active' => 'boolean',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            MasterCategory::class,
            'category_id'
        );
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(
            Currency::class,
            'currency_id'
        );
    }

    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(
            PaymentTerm::class,
            'default_payment_term_id'
        );
    }

    public function arAccount(): BelongsTo
    {
        return $this->belongsTo(
            ChartOfAccount::class,
            'ar_account_id'
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

    public function scopePreferred(Builder $query): Builder
    {
        return $query->where('is_preferred', true);
    }

    public function scopeBlacklisted(Builder $query): Builder
    {
        return $query->where('is_blacklisted', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return "{$this->customer_code} - {$this->customer_name}";
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address,
            $this->city,
            $this->province,
            $this->country,
            $this->postal_code,
        ])
        ->filter()
        ->implode(', ');
    }
}