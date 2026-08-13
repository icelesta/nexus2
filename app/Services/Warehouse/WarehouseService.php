<?php

namespace App\Services\Warehouse;

use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    /**
     * Create Warehouse
     */
    public function create(array $data): Warehouse
    {
        return DB::transaction(function () use ($data) {

            return Warehouse::create($data);

        });
    }

    /**
     * Update Warehouse
     */
    public function update(Warehouse $warehouse, array $data): Warehouse
    {
        return DB::transaction(function () use ($warehouse, $data) {

            $warehouse->update($data);

            return $warehouse->fresh();

        });
    }

    /**
     * Activate Warehouse
     */
    public function activate(Warehouse $warehouse): void
    {
        $warehouse->update([
            'is_active' => true,
        ]);
    }

    /**
     * Deactivate Warehouse
     */
    public function deactivate(Warehouse $warehouse): void
    {
        $warehouse->update([
            'is_active' => false,
        ]);
    }

    /**
     * Set Default Warehouse
     */
    public function setDefault(Warehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse) {

            Warehouse::query()->update([
                'is_default' => false,
            ]);

            $warehouse->update([
                'is_default' => true,
            ]);

        });
    }
}