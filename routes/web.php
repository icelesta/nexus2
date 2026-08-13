<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NotificationController;

use App\Http\Controllers\Purchasing\PurchaseOrderPrintController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Nexus ERP 2.0
|
| Purchasing Module
|
*/

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Purchase Order Document Engine
|--------------------------------------------------------------------------
*/

Route::middleware([
    'web',
    'auth',
])

    ->prefix('purchase-orders')

    ->name('purchase-orders.')

    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Preview
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/{purchaseOrder}/preview',
            [PurchaseOrderPrintController::class, 'preview']
        )->name('preview');

        /*
        |--------------------------------------------------------------------------
        | Browser Print
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/{purchaseOrder}/print',
            [PurchaseOrderPrintController::class, 'print']
        )->name('print');

        /*
        |--------------------------------------------------------------------------
        | Export PDF
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/{purchaseOrder}/export',
            [PurchaseOrderPrintController::class, 'export']
        )->name('export');

    });

    Route::middleware('auth')->group(function () {

        Route::get(
            '/notifications/{notification}/open',
            [NotificationController::class, 'open']
        )
            ->name('notifications.open');

        Route::post(
            '/notifications/read-all',
            [NotificationController::class, 'markAllAsRead']
        )
            ->name('notifications.read-all');

    });    