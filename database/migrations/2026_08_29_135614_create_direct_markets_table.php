<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('direct_markets')) {
            return;
        }

        Schema::create('direct_markets', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Document Information
            |--------------------------------------------------------------------------
            */

            $table->uuid('uuid');

            $table->string('dm_no', 50);

            /*
            |--------------------------------------------------------------------------
            | Organizational Relations
            |--------------------------------------------------------------------------
            */

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnUpdate();

            $table->foreignId('business_unit_id')
                ->nullable()
                ->constrained('business_units')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('cost_center_id')
                ->nullable()
                ->constrained('cost_centers')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            /*
            |--------------------------------------------------------------------------
            | Delivery / Reference
            |--------------------------------------------------------------------------
            */

            $table->string('delivery_location')
                ->nullable();

            $table->string('reference_no')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Request Information
            |--------------------------------------------------------------------------
            */

            $table->date('request_date');

            $table->date('required_date')
                ->nullable();

            $table->foreignId('requester_id')
                ->constrained('users')
                ->cascadeOnUpdate();

            /*
            |--------------------------------------------------------------------------
            | Workflow
            |--------------------------------------------------------------------------
            */

            $table->string('status', 30)
                ->default('Draft');

            $table->foreignId('submitted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

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
            | Unique Keys
            |--------------------------------------------------------------------------
            */

            $table->unique(
                'uuid',
                'uq_direct_markets_uuid'
            );

            $table->unique(
                'dm_no',
                'uq_direct_markets_dm_no'
            );

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(
                'company_id',
                'idx_dm_company'
            );

            $table->index(
                'business_unit_id',
                'idx_dm_business_unit'
            );

            $table->index(
                'branch_id',
                'idx_dm_branch'
            );

            $table->index(
                'department_id',
                'idx_dm_department'
            );

            $table->index(
                'cost_center_id',
                'idx_dm_cost_center'
            );

            $table->index(
                'requester_id',
                'idx_dm_requester'
            );

            $table->index(
                'status',
                'idx_dm_status'
            );

            $table->index(
                'request_date',
                'idx_dm_request_date'
            );

            $table->index(
                'required_date',
                'idx_dm_required_date'
            );

            $table->index(
                [
                    'company_id',
                    'status',
                    'request_date',
                ],
                'idx_dm_company_status_date'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_markets');
    }
};