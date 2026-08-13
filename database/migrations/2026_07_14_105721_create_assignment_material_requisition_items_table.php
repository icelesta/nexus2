<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_material_requisition_items', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Primary Key
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('assignment_material_requisition_id');

            $table->unsignedBigInteger('purchase_requisition_item_id');

            $table->unsignedBigInteger('supplier_id')
                ->nullable();

            $table->foreign(
                'assignment_material_requisition_id',
                'fk_amri_assignment'
            )
                ->references('id')
                ->on('assignment_material_requisitions')
                ->cascadeOnDelete();

            $table->foreign(
                'purchase_requisition_item_id',
                'fk_amri_pr_item'
            )
                ->references('id')
                ->on('purchase_requisition_items')
                ->cascadeOnDelete();

            $table->foreign(
                'supplier_id',
                'fk_amri_supplier'
            )
                ->references('id')
                ->on('suppliers')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Assignment Information
            |--------------------------------------------------------------------------
            */

            $table->decimal('assigned_qty', 18, 4)
                ->default(0);

            $table->decimal('quoted_price', 18, 2)
                ->default(0);

            $table->string('quotation_number', 100)
                ->nullable();

            $table->date('quotation_date')
                ->nullable();

            $table->integer('lead_time_days')
                ->nullable();

            $table->date('delivery_date')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Commercial Information
            |--------------------------------------------------------------------------
            */

            $table->decimal('discount_percent', 8, 2)
                ->default(0);

            $table->decimal('discount_amount', 18, 2)
                ->default(0);

            $table->decimal('tax_percent', 8, 2)
                ->default(0);

            $table->decimal('tax_amount', 18, 2)
                ->default(0);

            $table->decimal('line_total', 18, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Buyer Decision
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_selected_supplier')
                ->default(false);

            $table->text('buyer_notes')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Workflow
            |--------------------------------------------------------------------------
            */

            $table->string('status', 30)
                ->default('Draft');

            $table->string('approval_status', 30)
                ->default('Pending');

            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            $table->text('remarks')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Audit Trail
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->unsignedBigInteger('updated_by')
                ->nullable();

            $table->unsignedBigInteger('deleted_by')
                ->nullable();

            $table->foreign(
                'created_by',
                'fk_amri_created_by'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign(
                'updated_by',
                'fk_amri_updated_by'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign(
                'deleted_by',
                'fk_amri_deleted_by'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(
                'purchase_requisition_item_id',
                'idx_amri_pri'
            );

            $table->index(
                'supplier_id',
                'idx_amri_supplier'
            );

            $table->index(
                'status',
                'idx_amri_status'
            );

            $table->index(
                'approval_status',
                'idx_amri_approval'
            );

            $table->index(
                'delivery_date',
                'idx_amri_delivery'
            );

            $table->index(
                [
                    'assignment_material_requisition_id',
                    'status',
                ],
                'idx_amri_amr_status'
            );

            $table->index(
                [
                    'supplier_id',
                    'status',
                ],
                'idx_amri_supplier_status'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_material_requisition_items');
    }
};