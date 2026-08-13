<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ApprovalMaster extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'approval_masters';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'code',
        'name',
        'module',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function steps(): HasMany
    {
        return $this->hasMany(
            ApprovalMasterStep::class,
            'approval_master_id'
        )->orderBy('approval_level');
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
    | Scopes
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

    public function scopeForModule(
        Builder $query,
        string $module
    ): Builder {
        return $query
            ->where('module', $module);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function hasSteps(): bool
    {
        return $this->steps()->exists();
    }

    public function getLastApprovalLevel(): ?int
    {
        return $this->steps()
            ->max('approval_level');
    }

    public function canBeDeleted(): bool
    {
        /*
        |--------------------------------------------------------------------------
        | Active master cannot be deleted.
        |--------------------------------------------------------------------------
        */

        if ($this->is_active) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Future transaction usage check.
        |--------------------------------------------------------------------------
        |
        | Approval transaction relation will be added when AM-4
        | is implemented.
        |
        */

        if (
            method_exists($this, 'approvalTransactions')
            && $this->approvalTransactions()->exists()
        ) {
            return false;
        }

        return true;
    }

    public function approvalTransactions(): HasMany
    {
        return $this->hasMany(
            ApprovalTransaction::class,
            'approval_master_id'
        );
    }

}