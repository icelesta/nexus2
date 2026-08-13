<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTerm extends Model
{
    use SoftDeletes;

    protected $table = 'payment_terms';

    protected $fillable = [

        'uuid',

        'company_id',

        'term_code',
        'term_name',

        'description',

        'due_days',
        'discount_days',
        'discount_percent',

        'down_payment_percent',

        'installment_count',

        'grace_period_days',

        'sort_order',

        'is_default',
        'is_active',

        'remarks',

        'created_by',
        'updated_by',

    ];

    protected function casts(): array
    {
        return [

            'due_days'             => 'integer',
            'discount_days'        => 'integer',
            'discount_percent'     => 'decimal:2',
            'down_payment_percent' => 'decimal:2',
            'installment_count'    => 'integer',
            'grace_period_days'    => 'integer',
            'sort_order'           => 'integer',

            'is_default'           => 'boolean',
            'is_active'            => 'boolean',

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
            Company::class,
            'company_id'
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

    public function suppliers(): HasMany
    {
        return $this->hasMany(
            Supplier::class,
            'default_payment_term_id'
        );
    }

    public function customers(): HasMany
    {
        return $this->hasMany(
            Customer::class,
            'default_payment_term_id'
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

    public function scopeCompany(
        Builder $query,
        int $companyId
    ): Builder {
        return $query->where(
            'company_id',
            $companyId
        );
    }

    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query
            ->orderBy('sort_order')
            ->orderBy('term_code');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return "{$this->term_code} - {$this->term_name}";
    }

    public function getPaymentDescriptionAttribute(): string
    {
        if (
            $this->discount_percent > 0 &&
            $this->discount_days > 0
        ) {

            return "{$this->due_days} Days ({$this->discount_percent}% if paid within {$this->discount_days} Days)";
        }

        return "{$this->due_days} Days";
    }
}