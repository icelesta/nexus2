<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalTransactionStep extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'approval_transaction_steps';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'approval_transaction_id',
        'approval_master_step_id',
        'approval_level',
        'role_id',
        'role_code',
        'role_name',
        'approved_by',
        'status',
        'action',
        'acted_at',
        'remarks',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'approval_transaction_id' => 'integer',
            'approval_master_step_id' => 'integer',
            'approval_level'          => 'integer',
            'role_id'                 => 'integer',
            'approved_by'             => 'integer',
            'acted_at'                => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(
            ApprovalTransaction::class,
            'approval_transaction_id'
        );
    }

    public function approvalMasterStep(): BelongsTo
    {
        return $this->belongsTo(
            ApprovalMasterStep::class,
            'approval_master_step_id'
        );
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(
            \Spatie\Permission\Models\Role::class,
            'role_id'
        );
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'PENDING'
        );
    }

    public function scopeApproved(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'APPROVED'
        );
    }

    public function scopeRejected(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'REJECTED'
        );
    }

    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query->orderBy(
            'approval_level'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isRejected(): bool
    {
        return $this->status === 'REJECTED';
    }
}