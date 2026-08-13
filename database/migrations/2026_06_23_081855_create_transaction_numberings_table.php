<?php

declare(strict_types=1);

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
        Schema::create('transaction_numberings', function (Blueprint $table): void {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */

            $table->id();
            $table->uuid('uuid')->unique();

            /*
            |--------------------------------------------------------------------------
            | ORGANIZATION
            |--------------------------------------------------------------------------
            */

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->foreignId('business_unit_id')
                ->nullable()
                ->constrained('business_units')
                ->nullOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | DOCUMENT
            |--------------------------------------------------------------------------
            */

            $table->string('module', 100);

            $table->string('document_type', 100);

            $table->string('document_name', 150);

            /*
            |--------------------------------------------------------------------------
            | NUMBER FORMAT
            |--------------------------------------------------------------------------
            */

            $table->string('prefix', 30);

            $table->string('suffix', 30)
                ->nullable();

            $table->string('separator', 5)
                ->default('/');

            $table->string('format_pattern', 255)
                ->default('{PREFIX}/{YYYY}/{MM}/{RUNNING}');

            /*
            |--------------------------------------------------------------------------
            | COUNTER
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('running_digits')
                ->default(6);

            $table->unsignedBigInteger('start_number')
                ->default(1);

            $table->unsignedBigInteger('current_number')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | RESET
            |--------------------------------------------------------------------------
            */

            $table->enum('reset_type', [
                'Never',
                'Daily',
                'Monthly',
                'Yearly',
            ])->default('Monthly');

            $table->timestamp('last_generated_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            $table->unsignedInteger('sort_order')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | REMARK
            |--------------------------------------------------------------------------
            */

            $table->text('remarks')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | AUDIT
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('deleted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | UNIQUE INDEX
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'company_id',
                'business_unit_id',
                'branch_id',
                'module',
                'document_type',
            ], 'tn_company_bu_branch_document_unique');

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('company_id');
            $table->index('business_unit_id');
            $table->index('branch_id');

            $table->index('module');
            $table->index('document_type');

            $table->index('is_active');
            $table->index('sort_order');

            $table->index([
                'company_id',
                'business_unit_id',
                'branch_id',
                'is_active',
            ], 'tn_lookup_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_numberings');
    }
};