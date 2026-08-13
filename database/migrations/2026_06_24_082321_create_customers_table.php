<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {

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

            $table->string('customer_code',50);

            $table->string('customer_name',200);

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('master_categories')
                ->nullOnDelete();

            $table->enum('customer_type',[
                'Company',
                'Individual',
                'Government',
                'Distributor',
                'Dealer',
            ])->default('Company');

            $table->string('tax_number',100)
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

            $table->string('phone',50)
                ->nullable();

            $table->string('mobile',50)
                ->nullable();

            $table->string('website')
                ->nullable();

            $table->string('contact_person',150)
                ->nullable();

            $table->string('contact_position',100)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | ADDRESS
            |--------------------------------------------------------------------------
            */

            $table->text('address')
                ->nullable();

            $table->string('city',100)
                ->nullable();

            $table->string('province',100)
                ->nullable();

            $table->string('country',100)
                ->default('Indonesia');

            $table->string('postal_code',20)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | FINANCE
            |--------------------------------------------------------------------------
            */

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            $table->foreignId('ar_account_id')
                ->nullable()
                ->constrained('chart_of_accounts')
                ->nullOnDelete();

            $table->foreignId('payment_term_id')
                ->nullable()
                ->constrained('payment_terms')
                ->nullOnDelete();

            $table->decimal('credit_limit',18,2)
                ->default(0);

            $table->decimal('opening_balance',18,2)
                ->default(0);

            $table->unsignedInteger('credit_days')
                ->default(0);

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

            $table->unique([
                'company_id',
                'customer_code',
            ]);

            $table->index('category_id');
            $table->index('customer_name');
            $table->index('currency_id');
            $table->index('payment_term_id');
            $table->index('ar_account_id');
            $table->index('tax_id');
            $table->index('is_preferred');
            $table->index('is_blacklisted');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};