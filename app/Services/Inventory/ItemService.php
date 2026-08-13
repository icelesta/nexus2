<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemService
{
    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    public function getAll(): Collection
    {
        return Item::query()
            ->orderBy('item_name')
            ->get();
    }

    public function getActive(): Collection
    {
        return Item::query()
            ->active()
            ->orderBy('item_name')
            ->get();
    }

    public function search(string $keyword): Collection
    {
        return Item::query()
            ->where(function ($query) use ($keyword) {
                $query
                    ->where('item_code', 'like', "%{$keyword}%")
                    ->orWhere('item_name', 'like', "%{$keyword}%")
                    ->orWhere('barcode', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%");
            })
            ->orderBy('item_name')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Lookup
    |--------------------------------------------------------------------------
    */

    public function find(int $id): ?Item
    {
        return Item::find($id);
    }

    public function findByCode(string $code): ?Item
    {
        return Item::query()
            ->where('item_code', strtoupper($code))
            ->first();
    }

    public function findByBarcode(string $barcode): ?Item
    {
        return Item::query()
            ->where('barcode', $barcode)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Item
    {
        return DB::transaction(function () use ($data) {

            if (empty($data['item_code'])) {
                $data['item_code'] = $this->generateItemCode();
            }

            return Item::create($data);
        });
    }

    public function update(
        Item $item,
        array $data
    ): bool {

        return DB::transaction(function () use ($item, $data) {
            return $item->update($data);
        });
    }

    public function delete(Item $item): bool
    {
        return DB::transaction(function () use ($item) {
            return (bool) $item->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public function activate(Item $item): bool
    {
        return $item->update([
            'is_active' => true,
        ]);
    }

    public function deactivate(Item $item): bool
    {
        return $item->update([
            'is_active' => false,
        ]);
    }

    public function toggle(Item $item): bool
    {
        return $item->update([
            'is_active' => ! $item->is_active,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    public function codeExists(
        string $code,
        ?int $ignoreId = null
    ): bool {

        return Item::query()
            ->when(
                $ignoreId,
                fn ($query) => $query->whereKeyNot($ignoreId)
            )
            ->where('item_code', strtoupper($code))
            ->exists();
    }

    public function nameExists(
        string $name,
        ?int $ignoreId = null
    ): bool {

        return Item::query()
            ->when(
                $ignoreId,
                fn ($query) => $query->whereKeyNot($ignoreId)
            )
            ->where('item_name', $name)
            ->exists();
    }

    public function skuExists(
        string $sku,
        ?int $ignoreId = null
    ): bool {

        return Item::query()
            ->when(
                $ignoreId,
                fn ($query) => $query->whereKeyNot($ignoreId)
            )
            ->where('sku', $sku)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    public function totalCount(): int
    {
        return Item::count();
    }

    public function activeCount(): int
    {
        return Item::query()
            ->active()
            ->count();
    }

    public function inactiveCount(): int
    {
        return Item::query()
            ->where('is_active', false)
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Utilities
    |--------------------------------------------------------------------------
    */

    public function nextSortOrder(): int
    {
        return (int) Item::max('sort_order') + 1;
    }

    public function generateItemCode(): string
    {
        $last = Item::query()
            ->latest('id')
            ->first();

        $number = $last
            ? ((int) Str::after($last->item_code, '-') + 1)
            : 1;

        return sprintf(
            'ITEM-%06d',
            $number
        );
    }

    public function duplicate(Item $item): Item
    {
        return DB::transaction(function () use ($item) {

            $clone = $item->replicate();

            $clone->item_code = $this->generateItemCode();

            $clone->item_name .= ' (COPY)';

            $clone->save();

            return $clone;
        });
    }
}