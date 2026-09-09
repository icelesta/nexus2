<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adm_limit_masters', function (Blueprint $table): void {

            $table->id();

            $table->decimal(
                'limit_amount',
                15,
                2
            )->default(1000000.00);

            $table->boolean(
                'is_active'
            )->default(true);

            $table->string(
                'description'
            )->nullable();

            $table->foreignId(
                'created_by'
            )
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId(
                'updated_by'
            )
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'adm_limit_masters'
        );
    }
};