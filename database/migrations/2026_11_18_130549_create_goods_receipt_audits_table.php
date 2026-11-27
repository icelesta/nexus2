<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipt_audits', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RR REFERENCES
            |--------------------------------------------------------------------------
            |
            | Intentionally no cascading foreign key to Goods Receipt.
            | Audit history must survive RR deletion.
            |
            */

            $table->unsignedBigInteger('goods_receipt_id');
            $table->unsignedBigInteger('goods_receipt_item_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | EVENT INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('event_type', 60);
            $table->string('field_name', 100)->nullable();

            $table->longText('old_value')->nullable();
            $table->longText('new_value')->nullable();

            $table->text('reason')->nullable();

            $table->json('metadata')->nullable();

            /*
            |--------------------------------------------------------------------------
            | ACTOR
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            |
            | Audit records are immutable.
            | Only created_at is required.
            |
            */

            $table->timestamp('created_at')->useCurrent();

            /*
            |--------------------------------------------------------------------------
            | INDEXES
            |--------------------------------------------------------------------------
            */

            $table->index(
                ['goods_receipt_id', 'created_at'],
                'idx_gra_receipt_created'
            );

            $table->index(
                ['goods_receipt_item_id', 'created_at'],
                'idx_gra_item_created'
            );

            $table->index('event_type', 'idx_gra_event_type');
            $table->index('user_id', 'idx_gra_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_audits');
    }
};