<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role;

class ApprovalMasterStep extends Model
{
    use HasFactory;

    protected $table = 'approval_master_steps';

    protected $fillable = [
        'approval_master_id',
        'approval_level',
        'role_id',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'approval_level' => 'integer',
            'is_required' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Approval Master
    |--------------------------------------------------------------------------
    */

    public function approvalMaster(): BelongsTo
    {
        return $this->belongsTo(
            ApprovalMaster::class,
            'approval_master_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Approval Role
    |--------------------------------------------------------------------------
    */

    public function role(): BelongsTo
    {
        return $this->belongsTo(
            Role::class,
            'role_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Explicit Approval Users
    |--------------------------------------------------------------------------
    |
    | An approval step is primarily configured by ROLE.
    |
    | Optional specific users can then be assigned to the step.
    |
    | If this relationship is empty:
    |
    |     all active users belonging to the configured role
    |     remain eligible.
    |
    | If this relationship contains users:
    |
    |     only those selected active users belonging to the
    |     configured role are eligible.
    |
    | The actual runtime authorization is enforced by the
    | ApprovalTransactionService.
    |
    */

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'approval_master_step_users',
            'approval_master_step_id',
            'user_id'
        )->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeRequired(
        Builder $query
    ): Builder {
        return $query->where(
            'is_required',
            true
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
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isRequired(): bool
    {
        return (bool) $this->is_required;
    }

    /*
    |--------------------------------------------------------------------------
    | Explicit User Restriction
    |--------------------------------------------------------------------------
    |
    | TRUE:
    |     This approval level has specific users assigned.
    |
    | FALSE:
    |     No specific users are assigned, therefore the existing
    |     role-based fallback remains active.
    |
    */

    public function hasExplicitUsers(): bool
    {
        return $this->users()->exists();
    }
}