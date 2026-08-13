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
        Schema::create('suppliers', function (Blueprint $table) {

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

            $table->string('supplier_code', 50)->unique();

            $table->string('supplier_name', 200);

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('master_categories')
                ->nullOnDelete();

            $table->string('company_type', 50)
                ->nullable();

            $table->string('contact_person', 150)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | TAX INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('tax_number', 100)
                ->nullable();

            $table->foreignId('tax_id')
                ->nullable()
                ->constrained('tax_masters')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | CONTACT
            |--------------------------------------------------------------------------
            */

            $table->string('email')
                ->nullable();

            $table->string('phone', 50)
                ->nullable();

            $table->string('mobile', 50)
                ->nullable();

            $table->string('website')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | ADDRESS
            |--------------------------------------------------------------------------
            */

            $table->text('address')
                ->nullable();

            $table->string('city', 100)
                ->nullable();

            $table->string('province', 100)
                ->nullable();

            $table->string('country', 100)
                ->nullable();

            $table->string('postal_code', 20)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | FINANCIAL
            |--------------------------------------------------------------------------
            */

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            $table->foreignId('payment_term_id')
                ->nullable()
                ->constrained('payment_terms')
                ->nullOnDelete();

            $table->foreignId('ap_account_id')
                ->nullable()
                ->constrained('chart_of_accounts')
                ->nullOnDelete();

            $table->decimal('credit_limit', 18, 2)
                ->default(0);

            $table->decimal('opening_balance', 18, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | BANK INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('bank_name', 100)
                ->nullable();

            $table->string('bank_account_no', 100)
                ->nullable();

            $table->string('bank_account_name', 200)
                ->nullable();

            $table->string('swift_code', 50)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | PROCUREMENT
            |--------------------------------------------------------------------------
            */

            $table->integer('lead_time')
                ->default(0);

            $table->decimal('vendor_rating', 3, 2)
                ->default(0);

            $table->boolean('allow_purchase')
                ->default(true);

            $table->boolean('allow_service')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_preferred')
                ->default(false);

            $table->boolean('is_blacklisted')
                ->default(false);

            $table->date('blacklist_date')
                ->nullable();

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
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('company_id');
            $table->index('supplier_code');
            $table->index('supplier_name');
            $table->index('category_id');
            $table->index('currency_id');
            $table->index('payment_term_id');
            $table->index('ap_account_id');
            $table->index('tax_id');
            $table->index('lead_time');
            $table->index('is_preferred');
            $table->index('is_blacklisted');
            $table->index('is_active');

            $table->unique([
                'company_id',
                'supplier_code',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};