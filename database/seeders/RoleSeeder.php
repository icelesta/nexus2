<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [

            'Super Admin',

            'System Admin',

            'Finance Manager',

            'Finance Staff',

            'Purchasing Manager',

            'Purchasing Staff',

            'Warehouse Manager',

            'Warehouse Staff',

            'Inventory Controller',

            'Sales Manager',

            'Sales Staff',

            'HR Manager',

            'HR Staff',

            'Department Head',

            'Employee',

        ];

        foreach ($roles as $role) {

            Role::firstOrCreate([
                'name'       => $role,
                'guard_name' => 'web',
            ]);

        }
    }
}