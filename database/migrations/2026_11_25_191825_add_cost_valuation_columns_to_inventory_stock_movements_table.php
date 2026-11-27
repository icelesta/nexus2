<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_stock_movements', function (Blueprint $table) {
            // Original purchase cost per unit, after discount.
            $table->decimal('purchase_unit_cost', 18, 4)
                ->nullable();

            // Original transaction currency, e.g. IDR or USD.
            $table->string('purchase_currency_code', 3)
                ->nullable();

            // Exchange rate snapshot from the PO.
            $table->decimal('exchange_rate', 18, 6)
                ->nullable();

            // Purchase unit cost converted into IDR.
            $table->decimal('unit_cost_idr', 18, 4)
                ->nullable();

            // Moving weighted average snapshots.
            $table->decimal('avg_cost_before', 18, 4)
                ->nullable();

            $table->decimal('avg_cost_after', 18, 4)
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_stock_movements', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_unit_cost',
                'purchase_currency_code',
                'exchange_rate',
                'unit_cost_idr',
                'avg_cost_before',
                'avg_cost_after',
            ]);
        });
    }
};