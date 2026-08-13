<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ItemCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItemCategoryObserver
{
    /**
     * Handle the ItemCategory "creating" event.
     */
    public function creating(ItemCategory $itemCategory): void
    {
        if (blank($itemCategory->uuid)) {
            $itemCategory->uuid = (string) Str::uuid();
        }

        if (Auth::check()) {
            $itemCategory->created_by = Auth::id();
            $itemCategory->updated_by = Auth::id();
        }

        if ($itemCategory->is_active === null) {
            $itemCategory->is_active = true;
        }

        if ($itemCategory->sort_order === null) {
            $itemCategory->sort_order = 0;
        }
    }

    /**
     * Handle the ItemCategory "created" event.
     */
    public function created(ItemCategory $itemCategory): void
    {
        //
    }

    /**
     * Handle the ItemCategory "updating" event.
     */
    public function updating(ItemCategory $itemCategory): void
    {
        if (Auth::check()) {
            $itemCategory->updated_by = Auth::id();
        }
    }

    /**
     * Handle the ItemCategory "updated" event.
     */
    public function updated(ItemCategory $itemCategory): void
    {
        //
    }

    /**
     * Handle the ItemCategory "deleting" event.
     */
    public function deleting(ItemCategory $itemCategory): void
    {
        //
    }

    /**
     * Handle the ItemCategory "deleted" event.
     */
    public function deleted(ItemCategory $itemCategory): void
    {
        //
    }

    /**
     * Handle the ItemCategory "restored" event.
     */
    public function restored(ItemCategory $itemCategory): void
    {
        //
    }

    /**
     * Handle the ItemCategory "force deleted" event.
     */
    public function forceDeleted(ItemCategory $itemCategory): void
    {
        //
    }
}