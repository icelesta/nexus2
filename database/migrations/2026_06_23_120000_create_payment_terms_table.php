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
        Schema::create('payment_terms', function (Blueprint $table) {

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

            $table->string('term_code', 50)->unique();

            $table->string('term_name', 200);

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | DUE DATE RULES
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('due_days')
                ->default(0);

            $table->unsignedInteger('grace_days')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | EARLY PAYMENT DISCOUNT
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('discount_days')
                ->default(0);

            $table->decimal('discount_percent', 8, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | PAYMENT METHOD
            |--------------------------------------------------------------------------
            */

            $table->enum('payment_method', [
                'Cash',
                'Bank Transfer',
                'Cheque',
                'Giro',
                'Virtual Account',
                'Credit Card',
            ])->default('Bank Transfer');

            /*
            |--------------------------------------------------------------------------
            | PAYMENT FREQUENCY
            |--------------------------------------------------------------------------
            */

            $table->enum('payment_frequency', [
                'One Time',
                'Weekly',
                'Monthly',
                'Quarterly',
                'Yearly',
            ])->default('One Time');

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_default')
                ->default(false);

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
            $table->index('term_code');
            $table->index('term_name');
            $table->index('payment_method');
            $table->index('payment_frequency');
            $table->index('is_default');
            $table->index('is_active');
            $table->index('sort_order');

            $table->unique([
                'company_id',
                'term_code',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_terms');
    }
};