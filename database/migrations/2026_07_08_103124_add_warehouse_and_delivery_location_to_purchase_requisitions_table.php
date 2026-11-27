<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Warehouse and delivery_location are already defined
     * in the original purchase_requisitions migration.
     *
     * This migration is retained for migration history compatibility.
     */
    public function up(): void
    {
        // No additional schema changes required.
    }

    public function down(): void
    {
        // No schema changes to reverse.
    }
};