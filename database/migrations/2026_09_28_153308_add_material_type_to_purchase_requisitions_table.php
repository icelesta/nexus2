<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table): void {
            if (! Schema::hasColumn('purchase_requisitions', 'material_type')) {
                $table->string('material_type', 20)
                    ->default('PRODUCT')
                    ->after('request_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table): void {
            if (Schema::hasColumn('purchase_requisitions', 'material_type')) {
                $table->dropColumn('material_type');
            }
        });
    }
};