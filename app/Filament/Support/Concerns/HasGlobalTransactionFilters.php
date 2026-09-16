<?php

declare(strict_types=1);

namespace App\Filament\Support\Concerns;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Branch;
use App\Models\Department;


trait HasGlobalTransactionFilters
{
    /*
    |--------------------------------------------------------------------------
    | Global Transaction Filter State
    |--------------------------------------------------------------------------
    */

    public ?int $globalBranchFilter = null;

    public ?int $globalDepartmentFilter = null;

    public ?int $globalSupplierFilter = null;

    public ?string $globalStatusFilter = null;

    public ?string $globalDateFrom = null;

    public ?string $globalDateTo = null;


    /*
    |--------------------------------------------------------------------------
    | Allowed Roles
    |--------------------------------------------------------------------------
    */

    protected function globalTransactionFilterAllowedRoles(): array
    {
        return [
            'Super Administrator',
            'Administrator',
            'Manager',
            'OM-OD',
            'Supervisor',
        ];
    }

    protected function isDepartmentRestrictedRole(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole([
            'Staff',
            'Dept Head',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Role Access
    |--------------------------------------------------------------------------
    */

    public function canUseGlobalTransactionFilters(): bool
    {
        return auth()->check();
    }


    /*
    |--------------------------------------------------------------------------
    | Initialize Filters
    |--------------------------------------------------------------------------
    */

    protected function initializeGlobalTransactionFilters(): void
    {
        $this->globalDateFrom ??= now()
            ->startOfMonth()
            ->toDateString();

        $this->globalDateTo ??= now()
            ->endOfMonth()
            ->toDateString();

        if ($this->isDepartmentRestrictedRole()) {
            $this->globalDepartmentFilter = auth()->user()->department_id;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Reset Filters
    |--------------------------------------------------------------------------
    */

    public function resetGlobalTransactionFilters(): void
    {
        $this->globalBranchFilter = null;

        $this->globalDepartmentFilter =
        $this->isDepartmentRestrictedRole()
            ? auth()->user()->department_id
            : null;

        $this->globalSupplierFilter = null;

        $this->globalStatusFilter = null;

        $this->globalDateFrom = now()
            ->startOfMonth()
            ->toDateString();

        $this->globalDateTo = now()
            ->endOfMonth()
            ->toDateString();
    }


    /*
    |--------------------------------------------------------------------------
    | Active Filter State
    |--------------------------------------------------------------------------
    */

    public function hasActiveGlobalTransactionFilters(): bool
    {
        return
            $this->globalBranchFilter !== null
            || $this->globalDepartmentFilter !== null
            || $this->globalSupplierFilter !== null
            || $this->globalStatusFilter !== null;
    }


    /*
    |--------------------------------------------------------------------------
    | Apply Global Filters
    |--------------------------------------------------------------------------
    |
    | $dateColumn:
    |   The transaction date field.
    |
    | $statusColumn:
    |   The status field for the transaction.
    |
    */

    protected function applyGlobalTransactionFilters(
        Builder $query,
        string $dateColumn,
        string $statusColumn = 'status',
    ): Builder {

        if (! $this->canUseGlobalTransactionFilters()) {
            return $query;
        }

        /*
        |--------------------------------------------------------------------------
        | Branch
        |--------------------------------------------------------------------------
        */

        if ($this->globalBranchFilter !== null) {
            $query->where(
                'branch_id',
                $this->globalBranchFilter
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Department
        |--------------------------------------------------------------------------
        */

        if ($this->globalDepartmentFilter !== null) {
            $query->where(
                'department_id',
                $this->globalDepartmentFilter
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */

        if (
            filled($this->globalDateFrom)
        ) {
            $query->whereDate(
                $dateColumn,
                '>=',
                $this->globalDateFrom
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */

        if (
            filled($this->globalDateTo)
        ) {
            $query->whereDate(
                $dateColumn,
                '<=',
                $this->globalDateTo
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Transaction Status
        |--------------------------------------------------------------------------
        */

        if (
            filled($this->globalStatusFilter)
        ) {
            $query->where(
                $statusColumn,
                $this->globalStatusFilter
            );
        }


        return $query;
    }


    public function getGlobalBranchOptionsProperty()
    {
        return Branch::query()
            ->orderBy('branch_name')
            ->pluck('branch_name', 'id');
    }


    public function getGlobalDepartmentOptionsProperty()
    {
        return Department::query()
            ->orderBy('department_name')
            ->pluck('department_name', 'id');
    }

    public function getGlobalSupplierOptionsProperty()
    {
        return \App\Models\Supplier::query()
            ->orderBy('supplier_name')
            ->pluck('supplier_name', 'id');
    }    

    /*
    |--------------------------------------------------------------------------
    | Status Options
    |--------------------------------------------------------------------------
    |
    | Override this method in MR / AMR / PO.
    |
    */

    public function getGlobalTransactionStatusOptions(): array
    {
        return [];
    }
}