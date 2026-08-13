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
        Schema::create('request_for_quotation_items', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relationship
            |--------------------------------------------------------------------------
            */

            $table->foreignId('request_for_quotation_id')
                ->constrained('request_for_quotations')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('purchase_requisition_item_id')
                ->constrained('purchase_requisition_items')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Item Information
            |--------------------------------------------------------------------------
            */

            $table->decimal('qty', 18, 4);

            $table->foreignId('uom_id')
                ->constrained('uoms')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Supplier Quotation
            |--------------------------------------------------------------------------
            */

            $table->decimal('unit_price', 18, 4)
                ->default(0);

            $table->decimal('discount', 18, 4)
                ->default(0);

            $table->decimal('tax', 18, 4)
                ->default(0);

            $table->integer('lead_time')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Audit Trail
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('deleted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('request_for_quotation_id', 'idx_rfqi_rfq');

            $table->index('purchase_requisition_item_id', 'idx_rfqi_pr_item');

            $table->index('item_id', 'idx_rfqi_item');

            $table->index('uom_id', 'idx_rfqi_uom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_for_quotation_items');
    }
};
