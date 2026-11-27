<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('description')
                ->nullable();

            $table->enum('event_type', [
                'MEETING',
                'REMINDER',
                'EVENT',
                'DEADLINE',
            ])->default('MEETING');

            $table->enum('visibility', [
                'PERSONAL',
                'USERS',
                'COMPANY',
            ])->default('PERSONAL');

            $table->dateTime('start_at');

            $table->dateTime('end_at')
                ->nullable();

            $table->boolean('all_day')
                ->default(false);

            $table->string('location')
                ->nullable();

            $table->string('meeting_url')
                ->nullable();

            $table->enum('priority', [
                'LOW',
                'MEDIUM',
                'HIGH',
                'URGENT',
            ])->default('MEDIUM');

            $table->enum('status', [
                'SCHEDULED',
                'COMPLETED',
                'CANCELLED',
            ])->default('SCHEDULED');

            /*
            |--------------------------------------------------------------------------
            | Optional Nexus Transaction Reference
            |--------------------------------------------------------------------------
            */

            $table->string('reference_type')
                ->nullable();

            $table->unsignedBigInteger('reference_id')
                ->nullable();

            $table->timestamps();

            $table->index([
                'start_at',
                'end_at',
            ]);

            $table->index([
                'created_by',
                'visibility',
            ]);

            $table->index([
                'reference_type',
                'reference_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};