<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionNumbering extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'transaction_numberings';

    protected $fillable = [

        'uuid',

        'company_id',
        'business_unit_id',
        'branch_id',

        'document_type',
        'document_name',

        'prefix',
        'suffix',

        'number_separator',

        'current_number',

        'reset_type',

        'format_pattern',

        'last_generated_at',

        'remarks',

        'is_active',

        'module',

        'running_digits',

        'start_number',

        'sort_order',

        'created_by',
        'updated_by',
        'deleted_by',

    ];

    protected function casts(): array
    {
        return [

            'company_id' => 'integer',
            'business_unit_id' => 'integer',
            'branch_id' => 'integer',

            'running_digits' => 'integer',
            'start_number' => 'integer',
            'sort_order' => 'integer',
            'current_number' => 'integer',

            'is_active' => 'boolean',

            'last_generated_at' => 'datetime',

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

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            'company_id'
        );
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(
            Branch::class,
            'branch_id'
        );
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(
            BusinessUnit::class,
            'business_unit_id'
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

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'deleted_by'
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

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopeModule(
        Builder $query,
        string $module,
    ): Builder {
        return $query->where('module', $module);
    }

    public function scopeDocumentType(
        Builder $query,
        string $documentType,
    ): Builder {
        return $query->where('document_type', $documentType);
    }

    public function scopeCompany(
        Builder $query,
        ?int $companyId,
    ): Builder {
        return $query->when(
            filled($companyId),
            fn (Builder $query) => $query->where('company_id', $companyId),
        );
    }

    public function scopeBusinessUnit(
        Builder $query,
        ?int $businessUnitId,
    ): Builder {
        return $query->when(
            filled($businessUnitId),
            fn (Builder $query) => $query->where('business_unit_id', $businessUnitId),
        );
    }

    public function scopeBranch(
        Builder $query,
        ?int $branchId,
    ): Builder {
        return $query->when(
            filled($branchId),
            fn (Builder $query) => $query->where('branch_id', $branchId),
        );
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('module')
            ->orderBy('document_type')
            ->orderBy('document_name');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return sprintf(
            '%s - %s',
            $this->document_type,
            $this->document_name,
        );
    }

    public function getPreviewAttribute(): string
    {
        return $this->formattedPreview();
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function nextRunningNumber(): string
    {
        return str_pad(
            (string) (($this->current_number ?? 0) + 1),
            $this->running_digits,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Get the timezone used for transaction numbering.
     *
     * Company timezone is the business timezone.
     * UTC is used as a safe fallback.
     */
    public function numberingTimezone(): string
    {
        $timezone = $this->company?->timezone;

        if (! filled($timezone)) {
            return 'UTC';
        }

        try {
            new \DateTimeZone($timezone);

            return $timezone;
        } catch (\Throwable) {
            return 'UTC';
        }
    }

    /**
     * Get current date/time in the numbering timezone.
     */
    public function numberingNow(): \Carbon\Carbon
    {
        return now()->setTimezone(
            $this->numberingTimezone()
        );
    }

    public function formattedPreview(): string
    {
        $pattern = $this->format_pattern
            ?: '{PREFIX}/{YYYY}/{MM}/{RUNNING}';

        $now = $this->numberingNow();

        return strtr($pattern, [

            '{PREFIX}' => strtoupper($this->prefix ?? ''),

            '{SUFFIX}' => strtoupper($this->suffix ?? ''),

            '{YYYY}' => $now->format('Y'),

            '{YY}' => $now->format('y'),

            '{MM}' => $now->format('m'),

            '{DD}' => $now->format('d'),

            '{RUNNING}' => $this->nextRunningNumber(),

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    public const DOC_PURCHASE_REQUISITION = 'PURCHASE_REQUISITION';

    public const DOC_ASSIGNMENT_MATERIAL = 'ASSIGNMENT_MATERIAL';

    public const DOC_REQUEST_FOR_QUOTATION = 'REQUEST_FOR_QUOTATION';

    public const DOC_PURCHASE_ORDER = 'PURCHASE_ORDER';

    public const DOC_GOODS_RECEIPT = 'GOODS_RECEIPT';

    public const DOC_GOODS_ISSUE = 'GOODS_ISSUE';

    public const DOC_STOCK_TRANSFER = 'STOCK_TRANSFER';

    public const DOC_INVENTORY_ADJUSTMENT = 'INVENTORY_ADJUSTMENT';

    public const DOC_JOURNAL_ENTRY = 'JOURNAL_ENTRY';

    public const MODULE_PROCUREMENT = 'PROCUREMENT';

    public const MODULE_INVENTORY = 'INVENTORY';

    public const MODULE_FINANCE = 'FINANCE';

    public const RESET_NEVER = 'Never';

    public const RESET_DAILY = 'Daily';

    public const RESET_MONTHLY = 'Monthly';

    public const RESET_YEARLY = 'Yearly';
}