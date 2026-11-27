<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ItemStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ItemStockObserver
{
    private function hasColumn(string $column): bool
    {
        return Schema::hasColumn('item_stocks', $column);
    }

    public function creating(ItemStock $record): void
    {
        if (
            $this->hasColumn('uuid') &&
            blank($record->uuid)
        ) {
            $record->uuid = (string) Str::uuid();
        }

        if (Auth::check()) {
            if ($this->hasColumn('created_by')) {
                $record->created_by = Auth::id();
            }

            if ($this->hasColumn('updated_by')) {
                $record->updated_by = Auth::id();
            }
        }
    }

    public function created(ItemStock $record): void
    {
        //
    }

    public function updating(ItemStock $record): void
    {
        if (
            Auth::check() &&
            $this->hasColumn('updated_by')
        ) {
            $record->updated_by = Auth::id();
        }
    }

    public function updated(ItemStock $record): void
    {
        //
    }

    public function deleting(ItemStock $record): void
    {
        if (
            Auth::check() &&
            $this->hasColumn('deleted_by')
        ) {
            $record->deleted_by = Auth::id();
            $record->saveQuietly();
        }
    }

    public function deleted(ItemStock $record): void
    {
        //
    }

    public function restoring(ItemStock $record): void
    {
        if ($this->hasColumn('deleted_by')) {
            $record->deleted_by = null;
        }
    }

    public function restored(ItemStock $record): void
    {
        //
    }

    public function forceDeleting(ItemStock $record): void
    {
        //
    }

    public function forceDeleted(ItemStock $record): void
    {
        //
    }
}