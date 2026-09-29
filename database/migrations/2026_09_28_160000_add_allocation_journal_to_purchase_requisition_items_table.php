<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_requisition_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('purchase_requisition_items', 'allocation_journal_id')) {
                $table->foreignId('allocation_journal_id')
                    ->nullable()
                    ->after('warehouse_id')
                    ->constrained('chart_of_accounts')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requisition_items', function (Blueprint $table): void {
            if (Schema::hasColumn('purchase_requisition_items', 'allocation_journal_id')) {
                $table->dropForeign(['allocation_journal_id']);
                $table->dropColumn('allocation_journal_id');
            }
        });
    }
};