<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'assignment_direct_market_documents',
            function (Blueprint $table): void {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | ADM RELATION
                |--------------------------------------------------------------------------
                */

                $table->unsignedBigInteger(
                    'assignment_direct_market_id'
                );

                $table->foreign(
                    'assignment_direct_market_id',
                    'fk_adm_documents_assignment'
                )
                    ->references('id')
                    ->on('assignment_direct_markets')
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | FILE INFORMATION
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'file_name'
                );

                $table->string(
                    'file_path'
                );

                $table->unsignedBigInteger(
                    'file_size'
                )->nullable();

                $table->string(
                    'document_type',
                    100
                )->default(
                    'SUPPLIER_QUOTATION'
                );

                /*
                |--------------------------------------------------------------------------
                | AUDIT
                |--------------------------------------------------------------------------
                */

                $table->unsignedBigInteger(
                    'uploaded_by'
                )->nullable();

                $table->foreign(
                    'uploaded_by',
                    'fk_adm_documents_uploaded_by'
                )
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();

                $table->timestamps();

                $table->softDeletes();

                /*
                |--------------------------------------------------------------------------
                | INDEXES
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'assignment_direct_market_id',
                    'idx_adm_documents_assignment'
                );

                $table->index(
                    'uploaded_by',
                    'idx_adm_documents_uploaded_by'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'assignment_direct_market_documents'
        );
    }
};