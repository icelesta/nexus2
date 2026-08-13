<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalTransaction extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'approval_transactions';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'approval_master_id',
        'document_type',
        'document_id',
        'document_no',
        'current_level',
        'status',
        'submitted_at',
        'completed_at',
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
            'document_id'  => 'integer',
            'current_level' => 'integer',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
            'created_by'   => 'integer',
            'updated_by'   => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function approvalMaster(): BelongsTo
    {
        return $this->belongsTo(
            ApprovalMaster::class,
            'approval_master_id'
        );
    }

    public function steps(): HasMany
    {
        return $this->hasMany(
            ApprovalTransactionStep::class,
            'approval_transaction_id'
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

    public function scopeCancelled(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'CANCELLED'
        );
    }

    public function scopeDocument(
        Builder $query,
        string $documentType,
        int $documentId
    ): Builder {
        return $query
            ->where('document_type', $documentType)
            ->where('document_id', $documentId);
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow Helpers
    |--------------------------------------------------------------------------
    */

    public function currentStep(): ?ApprovalTransactionStep
    {
        return $this->steps()
            ->where(
                'approval_level',
                $this->current_level
            )
            ->first();
    }

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

    public function isCancelled(): bool
    {
        return $this->status === 'CANCELLED';
    }
}