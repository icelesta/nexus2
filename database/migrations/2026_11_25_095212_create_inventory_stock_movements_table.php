<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_movements', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->foreignId('item_id')
                ->constrained('items')
                ->restrictOnDelete();

            $table->foreignId('warehouse_id')
                ->constrained('warehouses')
                ->restrictOnDelete();

            $table->foreignId('goods_receipt_id')
                ->nullable()
                ->constrained('goods_receipts')
                ->restrictOnDelete();

            $table->foreignId('goods_receipt_item_id')
                ->nullable()
                ->constrained('goods_receipt_items')
                ->restrictOnDelete();

            $table->string('movement_type', 30);

            $table->decimal('quantity', 18, 4);

            $table->decimal('balance_before', 18, 4);

            $table->decimal('balance_after', 18, 4);

            $table->string('reference_type', 50)->nullable();

            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('remarks')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('created_at')->useCurrent();

            $table->unique(
                'goods_receipt_item_id',
                'uq_ism_goods_receipt_item'
            );

            $table->index(
                ['item_id', 'warehouse_id', 'created_at'],
                'idx_ism_item_warehouse_date'
            );

            $table->index(
                ['movement_type', 'created_at'],
                'idx_ism_type_date'
            );

            $table->index(
                ['reference_type', 'reference_id'],
                'idx_ism_reference'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_movements');
    }
};