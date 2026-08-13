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
        Schema::create('bank_accounts', function (Blueprint $table) {

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
            | BANK INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('bank_code', 30);

            $table->string('bank_name', 150);

            $table->string('account_no', 100);

            $table->string('account_name', 200);

            $table->string('branch_name', 150)
                ->nullable();

            $table->string('swift_code', 50)
                ->nullable();

            $table->string('iban', 100)
                ->nullable();

            $table->text('bank_address')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | ACCOUNTING
            |--------------------------------------------------------------------------
            */

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            $table->foreignId('coa_id')
                ->nullable()
                ->constrained('chart_of_accounts')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | BUSINESS RULES
            |--------------------------------------------------------------------------
            */

            $table->enum('account_type', [
                'Cash',
                'Bank',
                'Virtual',
            ])->default('Bank');

            $table->enum('payment_method', [
                'Transfer',
                'Cheque',
                'Giro',
                'Cash',
            ])->default('Transfer');

            $table->boolean('allow_payment')
                ->default(true);

            $table->boolean('allow_receipt')
                ->default(true);

            $table->boolean('allow_transfer')
                ->default(true);

            $table->decimal('opening_balance', 18, 2)
                ->default(0);

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
            | DESCRIPTION
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

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | CONSTRAINTS
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'company_id',
                'bank_code',
            ]);

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('company_id');
            $table->index('bank_name');
            $table->index('currency_id');
            $table->index('coa_id');
            $table->index('account_type');
            $table->index('payment_method');
            $table->index('allow_payment');
            $table->index('allow_receipt');
            $table->index('allow_transfer');
            $table->index('is_default');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};