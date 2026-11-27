<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('description')
                ->nullable();

            $table->dateTime('due_at')
                ->nullable();

            $table->enum('priority', [
                'LOW',
                'MEDIUM',
                'HIGH',
                'URGENT',
            ])->default('MEDIUM');

            $table->enum('status', [
                'OPEN',
                'IN_PROGRESS',
                'COMPLETED',
                'CANCELLED',
            ])->default('OPEN');

            $table->timestamps();

            $table->index([
                'user_id',
                'status',
            ]);

            $table->index('due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_tasks');
    }
};