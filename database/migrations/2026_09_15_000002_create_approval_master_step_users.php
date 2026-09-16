<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_master_step_users', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('approval_master_step_id')
                ->constrained('approval_master_steps')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(
                ['approval_master_step_id', 'user_id'],
                'ams_users_unique'
            );

            $table->index('user_id', 'ams_users_user_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_master_step_users');
    }
};
