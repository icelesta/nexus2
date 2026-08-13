<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Logistics
            |--------------------------------------------------------------------------
            */

            $table->foreignId('warehouse_id')
                ->nullable()
                ->after('cost_center_id')
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('delivery_location', 255)
                ->nullable()
                ->after('warehouse_id');

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table) {

            $table->dropIndex(['warehouse_id']);

            $table->dropConstrainedForeignId('warehouse_id');

            $table->dropColumn('delivery_location');
        });
    }
};