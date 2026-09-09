<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('direct_market_items')) {
            return;
        }

        Schema::create('direct_market_items', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Document / Item Relations
            |--------------------------------------------------------------------------
            */

            $table->uuid('uuid');

            $table->foreignId('direct_market_id')
                ->constrained('direct_markets')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnUpdate();

            $table->foreignId('uom_id')
                ->constrained('uoms')
                ->cascadeOnUpdate();

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */

            $table->decimal('qty', 18, 4);

            /*
            |--------------------------------------------------------------------------
            | Delivery
            |--------------------------------------------------------------------------
            */

            $table->date('required_date')
                ->nullable();

            $table->string('delivery_location')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Workflow
            |--------------------------------------------------------------------------
            */

            $table->string('status', 30)
                ->default('Draft');

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
            | Unique Key
            |--------------------------------------------------------------------------
            */

            $table->unique(
                'uuid',
                'uq_dm_items_uuid'
            );

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(
                'direct_market_id',
                'idx_dmi_direct_market'
            );

            $table->index(
                'item_id',
                'idx_dmi_item'
            );

            $table->index(
                'uom_id',
                'idx_dmi_uom'
            );

            $table->index(
                'status',
                'idx_dmi_status'
            );

            $table->index(
                'required_date',
                'idx_dmi_required_date'
            );

            $table->index(
                [
                    'direct_market_id',
                    'status',
                ],
                'idx_dmi_dm_status'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_market_items');
    }
};