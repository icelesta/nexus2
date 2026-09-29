<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_requisition_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('purchase_requisition_items', 'requirement_type')) {
                $table->string('requirement_type', 20)
                    ->default('MATERIAL')
                    ->after('purchase_requisition_id');
            }

            if (! Schema::hasColumn('purchase_requisition_items', 'expense_account_id')) {
                $table->unsignedBigInteger('expense_account_id')
                    ->nullable()
                    ->after('warehouse_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requisition_items', function (Blueprint $table): void {
            if (Schema::hasColumn('purchase_requisition_items', 'expense_account_id')) {
                $table->dropColumn('expense_account_id');
            }

            if (Schema::hasColumn('purchase_requisition_items', 'requirement_type')) {
                $table->dropColumn('requirement_type');
            }
        });
    }
};