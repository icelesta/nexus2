<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table): void {
            if (! Schema::hasColumn('purchase_requisitions', 'requirement_type')) {
                $table->string('requirement_type', 20)
                    ->default('MATERIAL')
                    ->after('warehouse_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table): void {
            if (Schema::hasColumn('purchase_requisitions', 'requirement_type')) {
                $table->dropColumn('requirement_type');
            }
        });
    }
};