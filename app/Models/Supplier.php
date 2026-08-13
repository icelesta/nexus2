<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\AssignmentMaterialRequisitionItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'suppliers';

    /*
    |--------------------------------------------------------------------------
    | Primary Key
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        'uuid',

        /*
        |--------------------------------------------------------------------------
        | Company
        |--------------------------------------------------------------------------
        */

        'company_id',

        /*
        |--------------------------------------------------------------------------
        | General Information
        |--------------------------------------------------------------------------
        */

        'supplier_code',
        'supplier_name',

        'category_id',

        'company_type',

        'contact_person',

        /*
        |--------------------------------------------------------------------------
        | Tax Information
        |--------------------------------------------------------------------------
        */

        'tax_number',

        'tax_id',

        /*
        |--------------------------------------------------------------------------
        | Contact
        |--------------------------------------------------------------------------
        */

        'email',
        'phone',
        'mobile',
        'website',

        /*
        |--------------------------------------------------------------------------
        | Address
        |--------------------------------------------------------------------------
        */

        'address',
        'city',
        'province',
        'country',
        'postal_code',

        /*
        |--------------------------------------------------------------------------
        | Financial
        |--------------------------------------------------------------------------
        */

        'currency_id',

        'payment_term_id',

        'ap_account_id',

        'credit_limit',

        'opening_balance',

        /*
        |--------------------------------------------------------------------------
        | Bank
        |--------------------------------------------------------------------------
        */

        'bank_name',

        'bank_account_no',

        'bank_account_name',

        'swift_code',

        /*
        |--------------------------------------------------------------------------
        | Procurement
        |--------------------------------------------------------------------------
        */

        'lead_time',

        'vendor_rating',

        'allow_purchase',

        'allow_service',

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'is_preferred',

        'is_blacklisted',

        'blacklist_date',

        'is_active',

        'sort_order',

        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        'remarks',

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        'created_by',

        'updated_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | UUID
            |--------------------------------------------------------------------------
            */

            'uuid' => 'string',

            /*
            |--------------------------------------------------------------------------
            | Financial
            |--------------------------------------------------------------------------
            */

            'credit_limit'     => 'decimal:2',

            'opening_balance'  => 'decimal:2',

            'vendor_rating'    => 'decimal:2',

            /*
            |--------------------------------------------------------------------------
            | Procurement
            |--------------------------------------------------------------------------
            */

            'lead_time' => 'integer',

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'allow_purchase' => 'boolean',

            'allow_service'  => 'boolean',

            'is_preferred'   => 'boolean',

            'is_blacklisted' => 'boolean',

            'is_active'      => 'boolean',

            /*
            |--------------------------------------------------------------------------
            | Date
            |--------------------------------------------------------------------------
            */

            'blacklist_date' => 'date',

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

            'deleted_at' => 'datetime',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Company Relationship
    |--------------------------------------------------------------------------
    */

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            'company_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Master Data Relationship
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
            'payment_term_id'
        );
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(
            TaxMaster::class,
            'tax_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accounting Relationship
    |--------------------------------------------------------------------------
    */

    public function apAccount(): BelongsTo
    {
        return $this->belongsTo(
            ChartOfAccount::class,
            'ap_account_id'
        );
    }    

    /*
    |--------------------------------------------------------------------------
    | Audit Relationship
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

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'deleted_by'
        );
    }    

    /*
    |--------------------------------------------------------------------------
    | Transaction Relationship
    |--------------------------------------------------------------------------
    */

    public function itemSuppliers(): HasMany
    {
        return $this->hasMany(
            ItemSupplier::class,
            'supplier_id'
        );
    }    


    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Active Suppliers
     */
    public function scopeActive(
        Builder $query,
    ): Builder {

        return $query->where(
            'is_active',
            true
        );
    }

    /**
     * Preferred Suppliers
     */
    public function scopePreferred(
        Builder $query,
    ): Builder {

        return $query->where(
            'is_preferred',
            true
        );
    }

    /**
     * Blacklisted Suppliers
     */
    public function scopeBlacklisted(
        Builder $query,
    ): Builder {

        return $query->where(
            'is_blacklisted',
            true
        );
    }

    /**
     * Company Scope
     */
    public function scopeCompany(
        Builder $query,
        int $companyId,
    ): Builder {

        return $query->where(
            'company_id',
            $companyId
        );
    }

    /**
     * Category Scope
     */
    public function scopeCategory(
        Builder $query,
        int $categoryId,
    ): Builder {

        return $query->where(
            'category_id',
            $categoryId
        );
    }

    /**
     * Search Supplier
     */
    public function scopeSearch(
        Builder $query,
        string $keyword,
    ): Builder {

        return $query->where(function (
            Builder $query,
        ) use (
            $keyword,
        ) {

            $query

                ->where(
                    'supplier_code',
                    'like',
                    "%{$keyword}%"
                )

                ->orWhere(
                    'supplier_name',
                    'like',
                    "%{$keyword}%"
                );

        });
    }

    /**
     * Ordered
     */
    public function scopeOrdered(
        Builder $query,
    ): Builder {

        return $query

            ->orderBy(
                'supplier_code'
            )

            ->orderBy(
                'supplier_name'
            );
    }

    /**
     * Current
     */
    public function scopeCurrent(
        Builder $query,
    ): Builder {

        return $query

            ->active()

            ->ordered();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Supplier Display Name
     */
    
    public function getDisplayNameAttribute(): string
    {
        return sprintf(
            '%s - %s',
            $this->supplier_code,
            $this->supplier_name,
        );
    }

    /**
     * Complete Address
     */
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

    /**
     * Supplier Status
     */
    public function getStatusLabelAttribute(): string
    {
        if (! $this->is_active) {

            return 'Inactive';

        }

        if ($this->is_blacklisted) {

            return 'Blacklisted';

        }

        if ($this->is_preferred) {

            return 'Preferred';

        }

        return 'Active';
    }

    /**
     * Contact Information
     */
    public function getContactInfoAttribute(): string
    {
        return collect([

            $this->phone,

            $this->mobile,

            $this->email,

        ])

        ->filter()

        ->implode(' | ');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check whether supplier is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check whether supplier is preferred.
     */
    public function isPreferred(): bool
    {
        return $this->is_preferred;
    }

    /**
     * Check whether supplier is blacklisted.
     */
    public function isBlacklisted(): bool
    {
        return $this->is_blacklisted;
    }

    /**
     * Check whether supplier can be used for purchasing.
     */
    public function canPurchase(): bool
    {
        return
            $this->is_active
            && ! $this->is_blacklisted
            && $this->allow_purchase;
    }

    /**
     * Check whether supplier can provide services.
     */
    public function canProvideService(): bool
    {
        return
            $this->is_active
            && ! $this->is_blacklisted
            && $this->allow_service;
    }

    /**
     * Check whether supplier has credit limit.
     */
    public function hasCreditLimit(): bool
    {
        return
            (float) $this->credit_limit > 0;
    }

    /**
     * Check whether supplier has opening balance.
     */
    public function hasOpeningBalance(): bool
    {
        return
            (float) $this->opening_balance > 0;
    }

    /**
     * Check whether supplier has payment term.
     */
    public function hasPaymentTerm(): bool
    {
        return
            ! empty($this->payment_term_id);
    }

    /**
     * Check whether supplier has AP Account.
     */
    public function hasApAccount(): bool
    {
        return
            ! empty($this->ap_account_id);
    }

    /**
     * Check whether supplier has tax.
     */
    public function hasTax(): bool
    {
        return
            ! empty($this->tax_id);
    }

    /**
     * Check whether supplier has email.
     */
    public function hasEmail(): bool
    {
        return
            filled($this->email);
    }

    /**
     * Check whether supplier has phone number.
     */
    public function hasPhone(): bool
    {
        return
            filled($this->phone)
            || filled($this->mobile);
    }

    /**
     * Check whether supplier has website.
     */
    public function hasWebsite(): bool
    {
        return
            filled($this->website);
    }

    /**
     * Check whether supplier has complete address.
     */
    public function hasAddress(): bool
    {
        return
            filled($this->address);
    }

    /**
     * Assignment Material Requisition Items.
     */
    public function assignmentMaterialRequisitionItems(): HasMany
    {
        return $this->hasMany(
            AssignmentMaterialRequisitionItem::class,
            'supplier_id'
        );
    }


}