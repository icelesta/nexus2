<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->char('uuid', 36);
            $table->string('item_code', 50);
            $table->string('item_name', 255);
            $table->string('short_name', 100)->nullable();
            $table->string('search_name', 255)->nullable();
            $table->text('description')->nullable();

            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('manufacturer_id')->nullable();
            $table->unsignedBigInteger('base_uom_id');

            $table->string('item_type', 50)->default('Inventory');
            $table->string('stock_type', 30)->default('Stock');

            $table->decimal('minimum_stock', 18, 4)->default(0);
            $table->decimal('maximum_stock', 18, 4)->default(0);
            $table->decimal('reorder_level', 18, 4)->default(0);
            $table->decimal('reorder_quantity', 18, 4)->default(0);
            $table->decimal('safety_stock', 18, 4)->default(0);
            $table->decimal('economic_order_qty', 18, 4)->default(0);

            $table->unsignedInteger('lead_time_days')->default(0);

            $table->unsignedBigInteger('default_warehouse_id')->nullable();
            $table->unsignedBigInteger('warehouse_type_id')->nullable();
            $table->unsignedBigInteger('purchase_uom_id')->nullable();
            $table->unsignedBigInteger('sales_uom_id')->nullable();
            $table->unsignedBigInteger('purchase_currency_id')->nullable();
            $table->unsignedBigInteger('sales_currency_id')->nullable();

            $table->unsignedBigInteger('inventory_account_id')->nullable();
            $table->unsignedBigInteger('expense_account_id')->nullable();
            $table->unsignedBigInteger('income_account_id')->nullable();

            $table->string('country_of_origin', 100)->nullable();
            $table->string('hs_code', 50)->nullable();
            $table->string('part_number', 100)->nullable();
            $table->string('model_number', 100)->nullable();
            $table->string('drawing_number', 100)->nullable();
            $table->string('unique_number', 150)->nullable();
            $table->string('revision_number', 30)->nullable();

            $table->decimal('net_weight', 18, 4)->default(0);
            $table->decimal('gross_weight', 18, 4)->default(0);
            $table->decimal('length', 18, 4)->default(0);
            $table->decimal('width', 18, 4)->default(0);
            $table->decimal('height', 18, 4)->default(0);
            $table->decimal('volume', 18, 4)->default(0);

            $table->boolean('is_serialized')->nullable()->default(false);
            $table->boolean('is_batch_tracked')->nullable()->default(false);
            $table->boolean('is_barcoded')->nullable()->default(true);
            $table->boolean('is_qrcode')->nullable()->default(true);
            $table->boolean('is_inventory')->nullable()->default(true);
            $table->boolean('is_purchase')->nullable()->default(true);
            $table->boolean('is_sales')->nullable()->default(false);

            $table->boolean('is_asset')->nullable()->default(false);
            $table->boolean('is_service')->nullable()->default(false);
            $table->boolean('is_manufacturing')->nullable()->default(false);
            $table->boolean('is_rental')->nullable()->default(false);
            $table->boolean('is_active')->nullable()->default(true);

            $table->text('remarks')->nullable();
            $table->string('image', 255)->nullable();

            $table->string('barcode', 100)->nullable();
            $table->string('barcode_type', 30)->default('CODE128');
            $table->boolean('barcode_printed')->default(false);

            $table->string('qr_code', 255)->nullable();
            $table->string('qr_code_type', 30)->default('ITEM');
            $table->boolean('qr_printed')->default(false);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()
                ->useCurrent()
                ->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->unique('uuid', 'uk_uuid');
            $table->unique('item_code', 'uk_code');
            $table->index('item_name', 'idx_name');
            $table->index('category_id', 'idx_category');
            $table->index('brand_id', 'idx_brand');
            $table->index('manufacturer_id', 'idx_manufacturer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
