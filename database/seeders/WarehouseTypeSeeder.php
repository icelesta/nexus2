<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\WarehouseType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WarehouseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        $user = User::first();

        $data = [

            [
                'type_code' => 'RAW',
                'type_name' => 'Raw Material',
                'description' => 'Raw material warehouse',
                'icon' => 'heroicon-o-cube',
                'color' => '#3b82f6',

                'allow_receipt' => true,
                'allow_issue' => true,
                'allow_transfer' => true,
                'allow_production' => true,
                'allow_sales' => false,
                'allow_adjustment' => true,

                'sort_order' => 1,
                'is_default' => true,
                'is_active' => true,
            ],

            [
                'type_code' => 'WIP',
                'type_name' => 'Work In Process',
                'description' => 'Production warehouse',
                'icon' => 'heroicon-o-cog-6-tooth',
                'color' => '#f59e0b',

                'allow_receipt' => true,
                'allow_issue' => true,
                'allow_transfer' => true,
                'allow_production' => true,
                'allow_sales' => false,
                'allow_adjustment' => true,

                'sort_order' => 2,
                'is_default' => false,
                'is_active' => true,
            ],

            [
                'type_code' => 'FG',
                'type_name' => 'Finished Goods',
                'description' => 'Finished Goods Warehouse',
                'icon' => 'heroicon-o-archive-box',
                'color' => '#22c55e',

                'allow_receipt' => true,
                'allow_issue' => false,
                'allow_transfer' => true,
                'allow_production' => false,
                'allow_sales' => true,
                'allow_adjustment' => true,

                'sort_order' => 3,
                'is_default' => false,
                'is_active' => true,
            ],

            [
                'type_code' => 'QA',
                'type_name' => 'Quality Inspection',
                'description' => 'Inspection Warehouse',
                'icon' => 'heroicon-o-shield-check',
                'color' => '#8b5cf6',

                'allow_receipt' => true,
                'allow_issue' => false,
                'allow_transfer' => false,
                'allow_production' => false,
                'allow_sales' => false,
                'allow_adjustment' => true,

                'sort_order' => 4,
                'is_default' => false,
                'is_active' => true,
            ],

            [
                'type_code' => 'QTN',
                'type_name' => 'Quarantine',
                'description' => 'Quarantine Warehouse',
                'icon' => 'heroicon-o-lock-closed',
                'color' => '#ef4444',

                'allow_receipt' => true,
                'allow_issue' => false,
                'allow_transfer' => false,
                'allow_production' => false,
                'allow_sales' => false,
                'allow_adjustment' => true,

                'sort_order' => 5,
                'is_default' => false,
                'is_active' => true,
            ],

            [
                'type_code' => 'TRN',
                'type_name' => 'Transit',
                'description' => 'Transit Warehouse',
                'icon' => 'heroicon-o-truck',
                'color' => '#06b6d4',

                'allow_receipt' => false,
                'allow_issue' => false,
                'allow_transfer' => true,
                'allow_production' => false,
                'allow_sales' => false,
                'allow_adjustment' => false,

                'sort_order' => 6,
                'is_default' => false,
                'is_active' => true,
            ],

            [
                'type_code' => 'RET',
                'type_name' => 'Return',
                'description' => 'Return Warehouse',
                'icon' => 'heroicon-o-arrow-uturn-left',
                'color' => '#14b8a6',

                'allow_receipt' => true,
                'allow_issue' => false,
                'allow_transfer' => true,
                'allow_production' => false,
                'allow_sales' => false,
                'allow_adjustment' => true,

                'sort_order' => 7,
                'is_default' => false,
                'is_active' => true,
            ],

            [
                'type_code' => 'SCR',
                'type_name' => 'Scrap',
                'description' => 'Scrap Warehouse',
                'icon' => 'heroicon-o-trash',
                'color' => '#6b7280',

                'allow_receipt' => false,
                'allow_issue' => true,
                'allow_transfer' => false,
                'allow_production' => false,
                'allow_sales' => false,
                'allow_adjustment' => true,

                'sort_order' => 8,
                'is_default' => false,
                'is_active' => true,
            ],

        ];

        foreach ($data as $item) {

            WarehouseType::updateOrCreate(

                [
                    'type_code' => $item['type_code'],
                ],

                array_merge($item, [

                    'uuid' => (string) Str::uuid(),

                    'company_id' => $company?->id,

                    'remarks' => null,

                    'created_by' => $user?->id,

                    'updated_by' => $user?->id,

                ])

            );
        }
    }
}