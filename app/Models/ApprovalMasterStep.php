<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

class ApprovalMasterStep extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'approval_master_steps';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'approval_master_id',
        'approval_level',
        'role_id',
        'is_required',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'approval_level' => 'integer',
            'is_required' => 'boolean',
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

    public function role(): BelongsTo
    {
        return $this->belongsTo(
            Role::class,
            'role_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeRequired(Builder $query): Builder
    {
        return $query->where('is_required', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('approval_level');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isRequired(): bool
    {
        return (bool) $this->is_required;
    }

    /**
     * Get active users who can approve an Approval Master Step.
     */
    public function getApproversForMasterStep(
        \App\Models\ApprovalMasterStep $step
    ) {
        if (! $step->role_id) {
            return collect();
        }

        return User::query()
            ->where('is_active', true)
            ->whereHas(
                'roles',
                fn ($query) => $query
                    ->where('roles.id', $step->role_id)
                    ->where('roles.guard_name', 'web')
                    ->where('roles.is_active', true)
            )
            ->orderBy('name')
            ->get();
    }
    
}