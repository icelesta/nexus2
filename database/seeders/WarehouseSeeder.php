<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $company = Company::first();

            $branch = Branch::first();

            $user = User::first();

            $warehouseTypes = WarehouseType::pluck('id', 'type_code');

            $data = [

                [
                    'warehouse_code'       => 'RAW-001',
                    'warehouse_name'       => 'Raw Material Warehouse',
                    'warehouse_type_id'    => $warehouseTypes['RAW'] ?? null,
                    'allow_purchase'       => true,
                    'allow_sales'          => false,
                    'allow_transfer'       => true,
                    'allow_production'     => true,
                    'allow_negative_stock' => false,
                    'is_default'           => true,
                    'is_active'            => true,
                    'sort_order'           => 1,
                ],

                [
                    'warehouse_code'       => 'WIP-001',
                    'warehouse_name'       => 'Work In Process Warehouse',
                    'warehouse_type_id'    => $warehouseTypes['WIP'] ?? null,
                    'allow_purchase'       => false,
                    'allow_sales'          => false,
                    'allow_transfer'       => true,
                    'allow_production'     => true,
                    'allow_negative_stock' => false,
                    'is_default'           => false,
                    'is_active'            => true,
                    'sort_order'           => 2,
                ],

                [
                    'warehouse_code'       => 'FG-001',
                    'warehouse_name'       => 'Finished Goods Warehouse',
                    'warehouse_type_id'    => $warehouseTypes['FG'] ?? null,
                    'allow_purchase'       => false,
                    'allow_sales'          => true,
                    'allow_transfer'       => true,
                    'allow_production'     => false,
                    'allow_negative_stock' => false,
                    'is_default'           => false,
                    'is_active'            => true,
                    'sort_order'           => 3,
                ],

                [
                    'warehouse_code'       => 'QA-001',
                    'warehouse_name'       => 'Quality Inspection Warehouse',
                    'warehouse_type_id'    => $warehouseTypes['QA'] ?? null,
                    'allow_purchase'       => false,
                    'allow_sales'          => false,
                    'allow_transfer'       => true,
                    'allow_production'     => false,
                    'allow_negative_stock' => false,
                    'is_default'           => false,
                    'is_active'            => true,
                    'sort_order'           => 4,
                ],

                [
                    'warehouse_code'       => 'QTN-001',
                    'warehouse_name'       => 'Quarantine Warehouse',
                    'warehouse_type_id'    => $warehouseTypes['QTN'] ?? null,
                    'allow_purchase'       => false,
                    'allow_sales'          => false,
                    'allow_transfer'       => true,
                    'allow_production'     => false,
                    'allow_negative_stock' => false,
                    'is_default'           => false,
                    'is_active'            => true,
                    'sort_order'           => 5,
                ],

                [
                    'warehouse_code'       => 'TRN-001',
                    'warehouse_name'       => 'Transit Warehouse',
                    'warehouse_type_id'    => $warehouseTypes['TRN'] ?? null,
                    'allow_purchase'       => false,
                    'allow_sales'          => false,
                    'allow_transfer'       => true,
                    'allow_production'     => false,
                    'allow_negative_stock' => false,
                    'is_default'           => false,
                    'is_active'            => true,
                    'sort_order'           => 6,
                ],

                [
                    'warehouse_code'       => 'RET-001',
                    'warehouse_name'       => 'Return Warehouse',
                    'warehouse_type_id'    => $warehouseTypes['RET'] ?? null,
                    'allow_purchase'       => false,
                    'allow_sales'          => false,
                    'allow_transfer'       => true,
                    'allow_production'     => false,
                    'allow_negative_stock' => false,
                    'is_default'           => false,
                    'is_active'            => true,
                    'sort_order'           => 7,
                ],

                [
                    'warehouse_code'       => 'SCR-001',
                    'warehouse_name'       => 'Scrap Warehouse',
                    'warehouse_type_id'    => $warehouseTypes['SCR'] ?? null,
                    'allow_purchase'       => false,
                    'allow_sales'          => false,
                    'allow_transfer'       => false,
                    'allow_production'     => false,
                    'allow_negative_stock' => false,
                    'is_default'           => false,
                    'is_active'            => true,
                    'sort_order'           => 8,
                ],

            ];

            foreach ($data as $warehouse) {

                if (empty($warehouse['warehouse_type_id'])) {
                    continue;
                }

                Warehouse::updateOrCreate(
                    [
                        'warehouse_code' => $warehouse['warehouse_code'],
                    ],
                    array_merge($warehouse, [

                        'uuid'       => (string) Str::uuid(),

                        'company_id' => $company?->id,

                        'branch_id'  => $branch?->id,

                        'created_by' => $user?->id,

                        'updated_by' => $user?->id,

                    ])
                );
            }
        });
    }
}