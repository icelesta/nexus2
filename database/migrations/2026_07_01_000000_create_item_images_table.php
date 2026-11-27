<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_images', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('item_id');
            $table->string('image_path', 255);
            $table->string('image_name', 255)->nullable();

            $table->boolean('is_primary')->nullable()->default(false);
            $table->integer('sort_order')->nullable()->default(0);

            $table->text('remarks')->nullable();

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()
                ->useCurrent()
                ->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_images');
    }
};
