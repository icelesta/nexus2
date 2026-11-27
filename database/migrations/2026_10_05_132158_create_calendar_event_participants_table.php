<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_event_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('calendar_event_id')
                ->constrained('calendar_events')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('response_status', [
                'PENDING',
                'ACCEPTED',
                'DECLINED',
                'TENTATIVE',
            ])->default('PENDING');

            $table->timestamp('responded_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'calendar_event_id',
                'user_id',
            ]);

            $table->index([
                'user_id',
                'response_status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_event_participants');
    }
};