<?php

namespace Database\Seeders;

use App\Models\BusinessUnit;
use Illuminate\Support\Facades\DB;

class BusinessUnitSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->bootSeeder();

        DB::transaction(function () {

            $data = [

                /*
                |--------------------------------------------------------------------------
                | FINANCE
                |--------------------------------------------------------------------------
                */
                [
                    'business_unit_code' => 'FIN',
                    'business_unit_name' => 'Finance',
                    'short_name'         => 'Finance',
                    'sort_order'         => 1,
                    'is_default'         => true,
                ],

                /*
                |--------------------------------------------------------------------------
                | HUMAN RESOURCE
                |--------------------------------------------------------------------------
                */
                [
                    'business_unit_code' => 'HR',
                    'business_unit_name' => 'Human Resource',
                    'short_name'         => 'HR',
                    'sort_order'         => 2,
                ],

                /*
                |--------------------------------------------------------------------------
                | PROCUREMENT
                |--------------------------------------------------------------------------
                */
                [
                    'business_unit_code' => 'PUR',
                    'business_unit_name' => 'Purchasing',
                    'short_name'         => 'Purchasing',
                    'sort_order'         => 3,
                ],

                /*
                |--------------------------------------------------------------------------
                | INVENTORY
                |--------------------------------------------------------------------------
                */
                [
                    'business_unit_code' => 'INV',
                    'business_unit_name' => 'Inventory',
                    'short_name'         => 'Inventory',
                    'sort_order'         => 4,
                ],

                /*
                |--------------------------------------------------------------------------
                | MANUFACTURING
                |--------------------------------------------------------------------------
                */
                [
                    'business_unit_code' => 'MFG',
                    'business_unit_name' => 'Manufacturing',
                    'short_name'         => 'Manufacturing',
                    'sort_order'         => 5,
                ],

                /*
                |--------------------------------------------------------------------------
                | SALES
                |--------------------------------------------------------------------------
                */
                [
                    'business_unit_code' => 'SAL',
                    'business_unit_name' => 'Sales',
                    'short_name'         => 'Sales',
                    'sort_order'         => 6,
                ],

                /*
                |--------------------------------------------------------------------------
                | PROJECT
                |--------------------------------------------------------------------------
                */
                [
                    'business_unit_code' => 'PRJ',
                    'business_unit_name' => 'Project',
                    'short_name'         => 'Project',
                    'sort_order'         => 7,
                ],

                /*
                |--------------------------------------------------------------------------
                | ENGINEERING
                |--------------------------------------------------------------------------
                */
                [
                    'business_unit_code' => 'ENG',
                    'business_unit_name' => 'Engineering',
                    'short_name'         => 'Engineering',
                    'sort_order'         => 8,
                ],

                /*
                |--------------------------------------------------------------------------
                | QUALITY ASSURANCE
                |--------------------------------------------------------------------------
                */
                [
                    'business_unit_code' => 'QA',
                    'business_unit_name' => 'Quality Assurance',
                    'short_name'         => 'QA',
                    'sort_order'         => 9,
                ],

                /*
                |--------------------------------------------------------------------------
                | INFORMATION TECHNOLOGY
                |--------------------------------------------------------------------------
                */
                [
                    'business_unit_code' => 'IT',
                    'business_unit_name' => 'Information Technology',
                    'short_name'         => 'IT',
                    'sort_order'         => 10,
                ],

            ];

            foreach ($data as $item) {

                $businessUnit = BusinessUnit::updateOrCreate(

                    [
                        'company_id'         => $this->company->id,
                        'business_unit_code' => $item['business_unit_code'],
                    ],

                    array_merge(

                        $item,

                        [

                            'branch_id'  => $this->branch?->id,

                            'manager_id' => $this->user->id,

                            'email'      => null,

                            'phone'      => null,

                            'address'    => null,

                            'remarks'    => null,

                            'is_active'  => true,

                        ],

                        $this->audit()

                    )

                );

                /*
                |--------------------------------------------------------------------------
                | UUID
                |--------------------------------------------------------------------------
                */

                if (blank($businessUnit->uuid)) {

                    $businessUnit->update([
                        'uuid' => $this->uuid(),
                    ]);

                }
            }

        });
    }
}