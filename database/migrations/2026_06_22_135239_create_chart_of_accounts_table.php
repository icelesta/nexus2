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
        Schema::create('chart_of_accounts', function (Blueprint $table) {

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
            | ACCOUNT
            |--------------------------------------------------------------------------
            */

            $table->string('account_code', 30)->unique();

            $table->string('account_name', 200);

            $table->text('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | HIERARCHY
            |--------------------------------------------------------------------------
            */

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('chart_of_accounts')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | ACCOUNT CLASSIFICATION
            |--------------------------------------------------------------------------
            */

            $table->enum('account_type', [
                'Assets',
                'Liabilities',
                'Equity',
                'Revenue',
                'Expense',
            ]);

            $table->string('account_group',100)->nullable();

            $table->enum('normal_balance',[
                'Debit',
                'Credit',
            ]);

            /*
            |--------------------------------------------------------------------------
            | CURRENCY
            |--------------------------------------------------------------------------
            */

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | BUSINESS RULES
            |--------------------------------------------------------------------------
            */

            $table->boolean('allow_posting')
                ->default(true);

            $table->boolean('require_cost_center')
                ->default(false);

            $table->boolean('require_profit_center')
                ->default(false);

            $table->boolean('require_department')
                ->default(false);

            $table->boolean('require_project')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_system')
                ->default(false);

            $table->boolean('is_control_account')
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
            $table->index('account_code');
            $table->index('account_name');
            $table->index('account_type');
            $table->index('account_group');
            $table->index('normal_balance');
            $table->index('currency_id');
            $table->index('parent_id');
            $table->index('is_active');

            $table->index([
                'company_id',
                'account_type',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};