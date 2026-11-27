<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_event_reminders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('calendar_event_id')
                ->constrained('calendar_events')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedInteger('minutes_before')
                ->default(15);

            $table->timestamp('sent_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'sent_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_event_reminders');
    }
};