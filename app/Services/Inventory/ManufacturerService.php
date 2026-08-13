<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Collection;

class ManufacturerService
{
    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    /**
     * Get all manufacturers.
     */
    public function getAll(): Collection
    {
        return Manufacturer::query()
            ->ordered()
            ->get();
    }

    /**
     * Get active manufacturers.
     */
    public function getActive(): Collection
    {
        return Manufacturer::query()
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Find by ID.
     */
    public function findById(int $id): ?Manufacturer
    {
        return Manufacturer::find($id);
    }

    /**
     * Find by UUID.
     */
    public function findByUuid(string $uuid): ?Manufacturer
    {
        return Manufacturer::query()
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * Search manufacturers.
     */
    public function search(string $keyword): Collection
    {
        return Manufacturer::query()
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
     * Check duplicate manufacturer code.
     */
    public function codeExists(
        string $code,
        ?int $ignoreId = null,
    ): bool {

        return Manufacturer::query()

            ->when(
                $ignoreId,
                fn ($query) => $query->whereKeyNot($ignoreId),
            )

            ->where(
                'manufacturer_code',
                strtoupper(trim($code)),
            )

            ->exists();
    }

    /**
     * Check duplicate manufacturer name.
     */
    public function nameExists(
        string $name,
        ?int $ignoreId = null,
    ): bool {

        return Manufacturer::query()

            ->when(
                $ignoreId,
                fn ($query) => $query->whereKeyNot($ignoreId),
            )

            ->where(
                'manufacturer_name',
                trim($name),
            )

            ->exists();
    }

    /**
     * Check UUID exists.
     */
    public function exists(string $uuid): bool
    {
        return Manufacturer::query()
            ->where('uuid', $uuid)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    /**
     * Activate manufacturer.
     */
    public function activate(
        Manufacturer $manufacturer,
    ): bool {

        return $manufacturer->update([
            'is_active' => true,
        ]);
    }

    /**
     * Deactivate manufacturer.
     */
    public function deactivate(
        Manufacturer $manufacturer,
    ): bool {

        return $manufacturer->update([
            'is_active' => false,
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggle(
        Manufacturer $manufacturer,
    ): bool {

        return $manufacturer->update([
            'is_active' => ! $manufacturer->is_active,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    /**
     * Count active manufacturers.
     */
    public function activeCount(): int
    {
        return Manufacturer::query()
            ->active()
            ->count();
    }

    /**
     * Count inactive manufacturers.
     */
    public function inactiveCount(): int
    {
        return Manufacturer::query()
            ->inactive()
            ->count();
    }

    /**
     * Count all manufacturers.
     */
    public function totalCount(): int
    {
        return Manufacturer::count();
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
        return ((int) Manufacturer::max('sort_order')) + 1;
    }
}