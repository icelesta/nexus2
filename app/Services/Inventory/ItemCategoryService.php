<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Models\ItemCategory;
use Illuminate\Database\Eloquent\Collection;

class ItemCategoryService
{
    /**
     * Get all active categories.
     */
    public function getActive(): Collection
    {
        return ItemCategory::query()
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Get root categories.
     */
    public function getRoot(): Collection
    {
        return ItemCategory::query()
            ->root()
            ->ordered()
            ->get();
    }

    /**
     * Check duplicate category code.
     */
    public function codeExists(
        string $code,
        ?int $ignoreId = null,
    ): bool {

        return ItemCategory::query()

            ->when(
                $ignoreId,
                fn ($query) => $query->whereKeyNot($ignoreId)
            )

            ->where(
                'category_code',
                $code
            )

            ->exists();
    }

    /**
     * Activate category.
     */
    public function activate(
        ItemCategory $category,
    ): bool {

        return $category->update([
            'is_active' => true,
        ]);
    }

    /**
     * Deactivate category.
     */
    public function deactivate(
        ItemCategory $category,
    ): bool {

        return $category->update([
            'is_active' => false,
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggle(
        ItemCategory $category,
    ): bool {

        return $category->update([
            'is_active' => ! $category->is_active,
        ]);
    }
}