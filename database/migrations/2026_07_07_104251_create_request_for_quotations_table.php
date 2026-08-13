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
        Schema::create('request_for_quotations', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Document Information
            |--------------------------------------------------------------------------
            */

            $table->uuid('uuid')->unique();

            $table->string('rfq_no', 50)->unique();

            /*
            |--------------------------------------------------------------------------
            | Relationship
            |--------------------------------------------------------------------------
            */

            $table->foreignId('purchase_requisition_id')
                ->constrained('purchase_requisitions')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            /*
            |--------------------------------------------------------------------------
            | RFQ Information
            |--------------------------------------------------------------------------
            */

            $table->date('rfq_date');

            $table->date('valid_until')->nullable();

            $table->string('status', 30)
                ->default('Draft');

            $table->text('remarks')->nullable();

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

            $table->index('purchase_requisition_id', 'idx_rfq_purchase_requisition');
            $table->index('supplier_id', 'idx_rfq_supplier');
            $table->index('status', 'idx_rfq_status');
            $table->index('rfq_date', 'idx_rfq_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_for_quotations');
    }
};