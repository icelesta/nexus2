<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_types', function (Blueprint $table): void {
            if (! Schema::hasColumn('warehouse_types', 'allow_receipt')) {
                $table->boolean('allow_receipt')
                    ->default(true)
                    ->after('allow_purchase');
            }

            if (! Schema::hasColumn('warehouse_types', 'allow_issue')) {
                $table->boolean('allow_issue')
                    ->default(true)
                    ->after('allow_receipt');
            }

            if (! Schema::hasColumn('warehouse_types', 'allow_adjustment')) {
                $table->boolean('allow_adjustment')
                    ->default(true)
                    ->after('allow_sales');
            }
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_types', function (Blueprint $table): void {
            if (Schema::hasColumn('warehouse_types', 'allow_adjustment')) {
                $table->dropColumn('allow_adjustment');
            }

            if (Schema::hasColumn('warehouse_types', 'allow_issue')) {
                $table->dropColumn('allow_issue');
            }

            if (Schema::hasColumn('warehouse_types', 'allow_receipt')) {
                $table->dropColumn('allow_receipt');
            }
        });
    }
};