<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PERMISSIONS = [
        'ViewAny:GeneratePurchaseOrder',
        'View:GeneratePurchaseOrder',
        'Update:GeneratePurchaseOrder',
    ];

    public function up(): void
    {
        foreach (self::PERMISSIONS as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }
    }

    public function down(): void
    {
        $permissions = Permission::query()
            ->whereIn('name', self::PERMISSIONS)
            ->where('guard_name', 'web')
            ->get();

        foreach ($permissions as $permission) {
            DB::table('role_has_permissions')
                ->where('permission_id', $permission->id)
                ->delete();

            DB::table('model_has_permissions')
                ->where('permission_id', $permission->id)
                ->delete();

            $permission->delete();
        }
    }
};
