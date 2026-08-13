<?php

namespace App\Services\BusinessUnit;

use App\Models\BusinessUnit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BusinessUnitService
{
    /**
     * Get all business units.
     */
    public function all(): Collection
    {
        return BusinessUnit::query()
            ->orderBy('sort_order')
            ->orderBy('business_unit_code')
            ->get();
    }

    /**
     * Paginate business units.
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return BusinessUnit::query()
            ->orderBy('sort_order')
            ->orderBy('business_unit_code')
            ->paginate($perPage);
    }

    /**
     * Find by ID.
     */
    public function find(int $id): ?BusinessUnit
    {
        return BusinessUnit::find($id);
    }

    /**
     * Find by UUID.
     */
    public function findByUuid(string $uuid): ?BusinessUnit
    {
        return BusinessUnit::where('uuid', $uuid)->first();
    }

    /**
     * Create Business Unit.
     */
    public function create(array $data): BusinessUnit
    {
        return DB::transaction(function () use ($data) {

            $data['uuid'] ??= (string) Str::uuid();

            return BusinessUnit::create($data);

        });
    }

    /**
     * Update Business Unit.
     */
    public function update(BusinessUnit $businessUnit, array $data): BusinessUnit
    {
        DB::transaction(function () use ($businessUnit, $data) {

            $businessUnit->update($data);

        });

        return $businessUnit->refresh();
    }

    /**
     * Delete Business Unit.
     */
    public function delete(BusinessUnit $businessUnit): bool
    {
        return (bool) $businessUnit->delete();
    }

    /**
     * Restore Business Unit.
     */
    public function restore(int $id): bool
    {
        $record = BusinessUnit::withTrashed()->findOrFail($id);

        return (bool) $record->restore();
    }

    /**
     * Force Delete.
     */
    public function forceDelete(int $id): bool
    {
        $record = BusinessUnit::withTrashed()->findOrFail($id);

        return (bool) $record->forceDelete();
    }

    /**
     * Toggle Active Status.
     */
    public function toggleActive(BusinessUnit $businessUnit): BusinessUnit
    {
        $businessUnit->update([
            'is_active' => ! $businessUnit->is_active,
        ]);

        return $businessUnit->refresh();
    }

    /**
     * Set Default Business Unit.
     */
    public function setDefault(BusinessUnit $businessUnit): BusinessUnit
    {
        DB::transaction(function () use ($businessUnit) {

            BusinessUnit::query()
                ->where('company_id', $businessUnit->company_id)
                ->update([
                    'is_default' => false,
                ]);

            $businessUnit->update([
                'is_default' => true,
            ]);

        });

        return $businessUnit->refresh();
    }

    /**
     * Active Business Units.
     */
    public function active(): Collection
    {
        return BusinessUnit::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('business_unit_name')
            ->get();
    }

    /**
     * Business Units by Company.
     */
    public function byCompany(int $companyId): Collection
    {
        return BusinessUnit::query()
            ->where('company_id', $companyId)
            ->orderBy('sort_order')
            ->orderBy('business_unit_name')
            ->get();
    }

    /**
     * Business Units by Branch.
     */
    public function byBranch(int $branchId): Collection
    {
        return BusinessUnit::query()
            ->where('branch_id', $branchId)
            ->orderBy('sort_order')
            ->orderBy('business_unit_name')
            ->get();
    }

    /**
     * Search Business Unit.
     */
    public function search(string $keyword): Collection
    {
        return BusinessUnit::query()

            ->where(function ($query) use ($keyword) {

                $query

                    ->where('business_unit_code', 'like', "%{$keyword}%")

                    ->orWhere('business_unit_name', 'like', "%{$keyword}%")

                    ->orWhere('short_name', 'like', "%{$keyword}%");

            })

            ->orderBy('business_unit_name')

            ->get();
    }

    /**
     * Count Active Business Units.
     */
    public function countActive(): int
    {
        return BusinessUnit::query()
            ->where('is_active', true)
            ->count();
    }

    /**
     * Count All Business Units.
     */
    public function count(): int
    {
        return BusinessUnit::count();
    }
}