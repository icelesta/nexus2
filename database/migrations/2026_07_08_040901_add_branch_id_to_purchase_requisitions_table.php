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
        Schema::table('purchase_requisitions', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Organization
            |--------------------------------------------------------------------------
            */

            $table->foreignId('branch_id')
                ->nullable()
                ->after('business_unit_id')
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index(
                'branch_id',
                'pr_branch_idx'
            );


            $table->index(
                [
                    'company_id',
                    'branch_id',
                    'status',
                    'request_date',
                ],
                'pr_comp_branch_stat_dt_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table) {

            $table->dropIndex(
                'pr_comp_branch_stat_dt_idx'
            );

            $table->dropIndex(
                'pr_branch_idx'
            );

            $table->dropConstrainedForeignId(
                'branch_id'
            );

            $table->index(
                [
                    'company_id',
                    'status',
                    'request_date',
                ],
                'pr_comp_stat_dt_idx'
            );
        });
    }
};