<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | GOODS RECEIPTS HEADER
        |--------------------------------------------------------------------------
        */

        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->string('items_status', 30)
                ->default('DRAFT')
                ->after('status');

            $table->string('rr_status', 30)
                ->default('DRAFT')
                ->after('items_status');
        });

        /*
        |--------------------------------------------------------------------------
        | GOODS RECEIPT ITEMS
        |--------------------------------------------------------------------------
        */

        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->string('item_status', 30)
                ->default('DRAFT')
                ->after('allocated_qty');

            $table->boolean('qc_passed')
                ->default(false)
                ->after('item_status');

            $table->foreignId('qc_checked_by')
                ->nullable()
                ->after('qc_passed')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('qc_checked_at')
                ->nullable()
                ->after('qc_checked_by');
        });

        /*
        |--------------------------------------------------------------------------
        | BACKFILL EXISTING ACTIVE RR ITEMS
        |--------------------------------------------------------------------------
        |
        | Status item dihitung berdasarkan total receiving kumulatif
        | dari semua RR aktif yang tidak CANCELLED.
        |
        | Data soft deleted tidak ikut perhitungan.
        | Kuantitas PO dan ADM tidak diubah.
        |
        */

        $activeItems = DB::table('goods_receipt_items as gri')
            ->join('goods_receipts as gr', 'gr.id', '=', 'gri.goods_receipt_id')
            ->whereNull('gri.deleted_at')
            ->whereNull('gr.deleted_at')
            ->whereRaw("UPPER(gr.status) <> 'CANCELLED'")
            ->select('gri.*')
            ->orderBy('gri.id')
            ->get();

        foreach ($activeItems as $item) {
            $sourceQty = null;
            $totalReceived = 0.0;

            if ($item->purchase_order_item_id !== null) {
                $sourceQty = DB::table('purchase_order_items')
                    ->where('id', $item->purchase_order_item_id)
                    ->value('ordered_qty');

                $totalReceived = (float) DB::table('goods_receipt_items as gri')
                    ->join('goods_receipts as gr', 'gr.id', '=', 'gri.goods_receipt_id')
                    ->where('gri.purchase_order_item_id', $item->purchase_order_item_id)
                    ->whereNull('gri.deleted_at')
                    ->whereNull('gr.deleted_at')
                    ->whereRaw("UPPER(gr.status) <> 'CANCELLED'")
                    ->sum('gri.received_qty');
            } elseif ($item->assignment_direct_market_item_id !== null) {
                $sourceQty = DB::table('assignment_direct_market_items')
                    ->where('id', $item->assignment_direct_market_item_id)
                    ->value('assigned_qty');

                $totalReceived = (float) DB::table('goods_receipt_items as gri')
                    ->join('goods_receipts as gr', 'gr.id', '=', 'gri.goods_receipt_id')
                    ->where(
                        'gri.assignment_direct_market_item_id',
                        $item->assignment_direct_market_item_id
                    )
                    ->whereNull('gri.deleted_at')
                    ->whereNull('gr.deleted_at')
                    ->whereRaw("UPPER(gr.status) <> 'CANCELLED'")
                    ->sum('gri.received_qty');
            }

            $sourceQty = $sourceQty !== null
                ? (float) $sourceQty
                : 0.0;

            $itemStatus = (
                $sourceQty > 0
                && $totalReceived >= ($sourceQty - 0.00005)
            )
                ? 'COMPLETED'
                : 'PARTIAL RECEIVED';

            DB::table('goods_receipt_items')
                ->where('id', $item->id)
                ->update([
                    'item_status' => $itemStatus,
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | BACKFILL EXISTING ACTIVE RR HEADERS
        |--------------------------------------------------------------------------
        */

        $activeReceipts = DB::table('goods_receipts')
            ->whereNull('deleted_at')
            ->whereRaw("UPPER(status) <> 'CANCELLED'")
            ->orderBy('id')
            ->get();

        foreach ($activeReceipts as $receipt) {
            $items = DB::table('goods_receipt_items')
                ->where('goods_receipt_id', $receipt->id)
                ->whereNull('deleted_at')
                ->get();

            if ($items->isEmpty()) {
                DB::table('goods_receipts')
                    ->where('id', $receipt->id)
                    ->update([
                        'items_status' => 'DRAFT',
                        'rr_status' => 'DRAFT',
                    ]);

                continue;
            }

            $allCompleted = $items->every(
                fn ($item) => $item->item_status === 'COMPLETED'
            );

            $itemsStatus = $allCompleted
                ? 'COMPLETED'
                : 'PARTIAL RECEIVED';

            /*
             * QC defaults to false.
             * Therefore, existing RR cannot be automatically CLOSED.
             */

            $rrStatus = 'RECEIVED';

            DB::table('goods_receipts')
                ->where('id', $receipt->id)
                ->update([
                    'items_status' => $itemsStatus,
                    'rr_status' => $rrStatus,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->dropForeign(['qc_checked_by']);

            $table->dropColumn([
                'item_status',
                'qc_passed',
                'qc_checked_by',
                'qc_checked_at',
            ]);
        });

        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->dropColumn([
                'items_status',
                'rr_status',
            ]);
        });
    }
};