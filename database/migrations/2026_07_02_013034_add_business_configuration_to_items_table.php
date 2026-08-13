<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Purchase
            |--------------------------------------------------------------------------
            */

            if (!Schema::hasColumn('items', 'is_subcontract')) {
                $table->boolean('is_subcontract')->default(false)->after('is_purchase');
            }

            if (!Schema::hasColumn('items', 'default_purchase_lead_time')) {
                $table->integer('default_purchase_lead_time')->default(0)->after('is_subcontract');
            }

            if (!Schema::hasColumn('items', 'purchase_tolerance')) {
                $table->decimal('purchase_tolerance', 18, 4)->default(0)->after('default_purchase_lead_time');
            }

            if (!Schema::hasColumn('items', 'minimum_purchase_qty')) {
                $table->decimal('minimum_purchase_qty', 18, 4)->default(1)->after('purchase_tolerance');
            }

            if (!Schema::hasColumn('items', 'purchase_requires_approval')) {
                $table->boolean('purchase_requires_approval')->default(false)->after('minimum_purchase_qty');
            }

            /*
            |--------------------------------------------------------------------------
            | Sales
            |--------------------------------------------------------------------------
            */

            if (!Schema::hasColumn('items', 'allow_discount')) {
                $table->boolean('allow_discount')->default(true)->after('is_sales');
            }

            if (!Schema::hasColumn('items', 'minimum_sales_qty')) {
                $table->decimal('minimum_sales_qty', 18, 4)->default(1)->after('allow_discount');
            }

            if (!Schema::hasColumn('items', 'sales_multiple')) {
                $table->decimal('sales_multiple', 18, 4)->default(1)->after('minimum_sales_qty');
            }

            if (!Schema::hasColumn('items', 'allow_backorder')) {
                $table->boolean('allow_backorder')->default(false)->after('sales_multiple');
            }

            if (!Schema::hasColumn('items', 'requires_serial_sales')) {
                $table->boolean('requires_serial_sales')->default(false)->after('allow_backorder');
            }

            /*
            |--------------------------------------------------------------------------
            | Inventory
            |--------------------------------------------------------------------------
            */

            if (!Schema::hasColumn('items', 'allow_negative_stock')) {
                $table->boolean('allow_negative_stock')->default(false)->after('economic_order_qty');
            }

            if (!Schema::hasColumn('items', 'cycle_count_required')) {
                $table->boolean('cycle_count_required')->default(false)->after('allow_negative_stock');
            }

            if (!Schema::hasColumn('items', 'quality_inspection_required')) {
                $table->boolean('quality_inspection_required')->default(false)->after('cycle_count_required');
            }

            /*
            |--------------------------------------------------------------------------
            | Compliance
            |--------------------------------------------------------------------------
            */

            if (!Schema::hasColumn('items', 'is_quality_control')) {
                $table->boolean('is_quality_control')->default(false)->after('quality_inspection_required');
            }

            if (!Schema::hasColumn('items', 'is_returnable')) {
                $table->boolean('is_returnable')->default(true)->after('is_quality_control');
            }

            if (!Schema::hasColumn('items', 'is_expirable')) {
                $table->boolean('is_expirable')->default(false)->after('is_returnable');
            }

            if (!Schema::hasColumn('items', 'is_hazardous')) {
                $table->boolean('is_hazardous')->default(false)->after('is_expirable');
            }

            if (!Schema::hasColumn('items', 'is_consignment')) {
                $table->boolean('is_consignment')->default(false)->after('is_hazardous');
            }

            if (!Schema::hasColumn('items', 'requires_certificate')) {
                $table->boolean('requires_certificate')->default(false)->after('is_consignment');
            }

        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {

            $table->dropColumn([
                'is_subcontract',
                'default_purchase_lead_time',
                'purchase_tolerance',
                'minimum_purchase_qty',
                'purchase_requires_approval',
                'allow_discount',
                'minimum_sales_qty',
                'sales_multiple',
                'allow_backorder',
                'requires_serial_sales',
                'allow_negative_stock',
                'cycle_count_required',
                'quality_inspection_required',
                'is_quality_control',
                'is_returnable',
                'is_expirable',
                'is_hazardous',
                'is_consignment',
                'requires_certificate',
            ]);

        });
    }
};