<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->foreignId('cost_center_id')
                ->nullable()
                ->after('section_id')
                ->constrained('cost_centers')
                ->nullOnDelete();

            $table->foreignId('profit_center_id')
                ->nullable()
                ->after('cost_center_id')
                ->constrained('profit_centers')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropConstrainedForeignId('profit_center_id');

            $table->dropConstrainedForeignId('cost_center_id');

        });
    }
};