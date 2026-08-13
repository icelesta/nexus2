<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;

class BrandService
{
    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    /**
     * Get all brands.
     */
    public function getAll(): Collection
    {
        return Brand::query()
            ->ordered()
            ->get();
    }

    /**
     * Get active brands.
     */
    public function getActive(): Collection
    {
        return Brand::query()
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Find by ID.
     */
    public function findById(int $id): ?Brand
    {
        return Brand::find($id);
    }

    /**
     * Find by UUID.
     */
    public function findByUuid(string $uuid): ?Brand
    {
        return Brand::query()
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * Search brands.
     */
    public function search(string $keyword): Collection
    {
        return Brand::query()
            ->search($keyword)
            ->ordered()
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    /**
     * Check duplicate brand code.
     */
    public function codeExists(
        string $code,
        ?int $ignoreId = null,
    ): bool {

        return Brand::query()

            ->when(
                $ignoreId,
                fn ($query) => $query->whereKeyNot($ignoreId),
            )

            ->where(
                'brand_code',
                strtoupper(trim($code)),
            )

            ->exists();
    }

    /**
     * Check duplicate brand name.
     */
    public function nameExists(
        string $name,
        ?int $ignoreId = null,
    ): bool {

        return Brand::query()

            ->when(
                $ignoreId,
                fn ($query) => $query->whereKeyNot($ignoreId),
            )

            ->where(
                'brand_name',
                trim($name),
            )

            ->exists();
    }

    /**
     * Check UUID exists.
     */
    public function exists(string $uuid): bool
    {
        return Brand::query()
            ->where('uuid', $uuid)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    /**
     * Activate.
     */
    public function activate(Brand $brand): bool
    {
        return $brand->update([
            'is_active' => true,
        ]);
    }

    /**
     * Deactivate.
     */
    public function deactivate(Brand $brand): bool
    {
        return $brand->update([
            'is_active' => false,
        ]);
    }

    /**
     * Toggle status.
     */
    public function toggle(Brand $brand): bool
    {
        return $brand->update([
            'is_active' => ! $brand->is_active,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Generate next sort order.
     */
    public function nextSortOrder(): int
    {
        return ((int) Brand::max('sort_order')) + 1;
    }

    /**
     * Active count.
     */
    public function activeCount(): int
    {
        return Brand::query()
            ->active()
            ->count();
    }

    /**
     * Inactive count.
     */
    public function inactiveCount(): int
    {
        return Brand::query()
            ->inactive()
            ->count();
    }

    /**
     * Total count.
     */
    public function totalCount(): int
    {
        return Brand::count();
    }
}