<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;
    use HasRoles;


    protected $fillable = [

        // Identity
        'employee_no',
        'username',
        'name',
        'email',
        'phone',
        'password',

        // Preferences
        'locale',
        'timezone',

        // Organization
        'company_id',
        'branch_id',
        'business_unit_id',
        'department_id',
        'section_id',
        'cost_center_id',
        'profit_center_id',

        // Status
        'is_active',

        // Audit
        'created_by',
        'updated_by',

    ];

    protected $hidden = [

        'password',
        'remember_token',

    ];

    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',

            'password' => 'hashed',

            'is_active' => 'boolean',

            'last_login_at' => 'datetime',

            'last_activity_at' => 'datetime',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helper
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return $this->hasAnyRole([
            'Super Administrator',
            'Administrator',
        ]);
    }



    /*
    |--------------------------------------------------------------------------
    | Organization Relationships
    |--------------------------------------------------------------------------
    */

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function profitCenter(): BelongsTo
    {
        return $this->belongsTo(ProfitCenter::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Audit Trail
    |--------------------------------------------------------------------------
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'created_by'
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
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

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
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

    public function scopeBranch(
        Builder $query,
        int $branchId
    ): Builder {
        return $query->where(
            'branch_id',
            $branchId
        );
    }

    public function scopeBusinessUnit(
        Builder $query,
        int $businessUnitId
    ): Builder {
        return $query->where(
            'business_unit_id',
            $businessUnitId
        );
    }

    public function scopeDepartment(
        Builder $query,
        int $departmentId
    ): Builder {
        return $query->where(
            'department_id',
            $departmentId
        );
    }

    public function scopeSection(
        Builder $query,
        int $sectionId
    ): Builder {
        return $query->where(
            'section_id',
            $sectionId
        );
    }

    public function scopeCostCenter(
        Builder $query,
        int $costCenterId
    ): Builder {
        return $query->where(
            'cost_center_id',
            $costCenterId
        );
    }

    public function scopeProfitCenter(
        Builder $query,
        int $profitCenterId
    ): Builder {
        return $query->where(
            'profit_center_id',
            $profitCenterId
        );
    }


    public function scopeOrganization(
        Builder $query,
        int $companyId,
        ?int $branchId = null,
        ?int $businessUnitId = null,
        ?int $departmentId = null
    ): Builder {

        $query->company($companyId);

        if ($branchId) {
            $query->branch($branchId);
        }

        if ($businessUnitId) {
            $query->businessUnit($businessUnitId);
        }

        if ($departmentId) {
            $query->department($departmentId);
        }

        return $query;
    } 

    public function scopeRole(
        Builder $query,
        string $role
    ): Builder {
        return $query->role($role);
    }    



    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        if ($this->employee_no) {
            return "{$this->employee_no} | {$this->username} | {$this->name}";
        }

        return $this->name;
    }

    public function getOrganizationAttribute(): string
    {
    return collect([
        $this->company?->display_name,
        $this->branch?->display_name,
        $this->businessUnit?->display_name,
        $this->department?->display_name,
        $this->section?->display_name,
        $this->costCenter?->display_name,
        $this->profitCenter?->display_name,
    ])
    ->filter()
    ->implode(' / ');
    }

    public function getShortNameAttribute(): string
    {
        return collect([
            $this->employee_no,
            $this->name,
        ])
        ->filter()
        ->implode(' - ');
    }

    // TODO:
    // public function employee(): BelongsTo
    // {
    //     return $this->belongsTo(Employee::class);
    // }

    // public function position(): BelongsTo
    // {
    //     return $this->belongsTo(Position::class);
    // }
    

}