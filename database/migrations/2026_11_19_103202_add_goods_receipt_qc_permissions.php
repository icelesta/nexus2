<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    private const PERMISSIONS = [
        'ViewQC:GoodsReceipt',
        'UpdateQC:GoodsReceipt',
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
        // Intentionally preserve permissions and role assignments.
        // Avoid destructive permission removal during rollback.
    }
};