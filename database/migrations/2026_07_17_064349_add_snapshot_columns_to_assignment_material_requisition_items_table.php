<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignment_material_requisition_items', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Item Snapshot
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('item_id')
                ->nullable()
                ->after('purchase_requisition_item_id');

            $table->string('item_code', 100)
                ->nullable()
                ->after('item_id');

            $table->string('item_name', 255)
                ->nullable()
                ->after('item_code');

            $table->text('item_description')
                ->nullable()
                ->after('item_name');

            $table->text('specification')
                ->nullable()
                ->after('item_description');

            /*
            |--------------------------------------------------------------------------
            | UOM Snapshot
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('uom_id')
                ->nullable()
                ->after('specification');

            $table->string('uom_code', 50)
                ->nullable()
                ->after('uom_id');

            /*
            |--------------------------------------------------------------------------
            | Quantity Snapshot
            |--------------------------------------------------------------------------
            */

            $table->decimal('requested_qty', 18, 4)
                ->default(0)
                ->after('assigned_qty');

            $table->decimal('approved_qty', 18, 4)
                ->default(0)
                ->after('requested_qty');

            $table->decimal('remaining_qty', 18, 4)
                ->default(0)
                ->after('approved_qty');

            /*
            |--------------------------------------------------------------------------
            | Requirement Snapshot
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('warehouse_id')
                ->nullable()
                ->after('remaining_qty');

            $table->date('required_date')
                ->nullable()
                ->after('warehouse_id');

            $table->string('delivery_location', 255)
                ->nullable()
                ->after('required_date');

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign('item_id', 'fk_amri_item')
                ->references('id')
                ->on('items')
                ->nullOnDelete();

            $table->foreign('uom_id', 'fk_amri_uom')
                ->references('id')
                ->on('uoms')
                ->nullOnDelete();

            $table->foreign('warehouse_id', 'fk_amri_wh')
                ->references('id')
                ->on('warehouses')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('item_id', 'idx_amri_item');

            $table->index('item_code', 'idx_amri_item_code');

            $table->index('warehouse_id', 'idx_amri_wh');

            $table->index(
                ['item_id', 'supplier_id'],
                'idx_amri_item_supplier'
            );
        });
    }

    public function down(): void
    {
        Schema::table('assignment_material_requisition_items', function (Blueprint $table) {

            $table->dropForeign('fk_amri_item');
            $table->dropForeign('fk_amri_uom');
            $table->dropForeign('fk_amri_wh');

            $table->dropIndex('idx_amri_item');
            $table->dropIndex('idx_amri_item_code');
            $table->dropIndex('idx_amri_wh');
            $table->dropIndex('idx_amri_item_supplier');

            $table->dropColumn([
                'item_id',
                'item_code',
                'item_name',
                'item_description',
                'specification',
                'uom_id',
                'uom_code',
                'requested_qty',
                'approved_qty',
                'remaining_qty',
                'warehouse_id',
                'required_date',
                'delivery_location',
            ]);
        });
    }
};