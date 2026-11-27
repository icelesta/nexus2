<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add optional Warehouse to Goods Receipt header.
        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->foreignId('warehouse_id')
                ->nullable()
                ->after('supplier_id')
                ->constrained('warehouses')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        // Make existing item Warehouse optional.
        // Preserve the existing foreign key and index.
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        if (
            DB::table('goods_receipts')->whereNull('warehouse_id')->exists()
            || DB::table('goods_receipt_items')->whereNull('warehouse_id')->exists()
        ) {
            throw new \RuntimeException(
                'Cannot rollback: NULL warehouse_id records still exist.'
            );
        }

        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')
                ->nullable(false)
                ->change();
        });

        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('warehouse_id');
        });
    }
};