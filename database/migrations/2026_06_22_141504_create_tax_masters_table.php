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
        Schema::create('tax_masters', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */

            $table->id();

            $table->uuid('uuid')->unique();

            /*
            |--------------------------------------------------------------------------
            | COMPANY
            |--------------------------------------------------------------------------
            */

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | GENERAL INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('tax_code', 30)->unique();

            $table->string('tax_name', 150);

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | TAX CONFIGURATION
            |--------------------------------------------------------------------------
            */

            $table->enum('tax_type', [
                'VAT',
                'WHT',
                'OTHER',
            ]);

            $table->decimal('tax_rate', 8, 4)
                ->default(0);

            $table->date('effective_date')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | ACCOUNTING
            |--------------------------------------------------------------------------
            */

            $table->foreignId('coa_input_tax_id')
                ->nullable()
                ->constrained('chart_of_accounts')
                ->nullOnDelete();

            $table->foreignId('coa_output_tax_id')
                ->nullable()
                ->constrained('chart_of_accounts')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | BUSINESS RULES
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_inclusive')
                ->default(false);

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

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('company_id');
            $table->index('tax_code');
            $table->index('tax_name');
            $table->index('tax_type');
            $table->index('effective_date');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_masters');
    }
};