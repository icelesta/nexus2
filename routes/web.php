<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NotificationController;

use App\Http\Controllers\Purchasing\PurchaseOrderPrintController;
use App\Http\Controllers\PurchaseRequisitionPdfController;

use App\Models\AssignmentMaterialRequisitionDocument;
use Illuminate\Support\Facades\Storage;
use Filament\Http\Middleware\Authenticate;


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
|
| Preview
| Browser Print
| Export PDF
|
*/


Route::middleware([
    'web',
    Authenticate::class,
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
        )
            ->name('preview');


        /*
        |--------------------------------------------------------------------------
        | Browser Print
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/{purchaseOrder}/print',
            [PurchaseOrderPrintController::class, 'print']
        )
            ->name('print');


        /*
        |--------------------------------------------------------------------------
        | Export PDF
        |--------------------------------------------------------------------------
        |
        | Final route:
        |
        | /purchase-orders/{purchaseOrder}/print/pdf
        |
        | Route name:
        |
        | purchase-orders.export
        |
        */

        Route::get(
            '/{purchaseOrder}/print/pdf',
            [PurchaseOrderPrintController::class, 'export']
        )
            ->name('export');

    });


/*
|--------------------------------------------------------------------------
| Notifications
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Open Notification
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/notifications/{notification}/open',
            [NotificationController::class, 'open']
        )
            ->name('notifications.open');


        /*
        |--------------------------------------------------------------------------
        | Mark All Notifications As Read
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/notifications/read-all',
            [NotificationController::class, 'markAllAsRead']
        )
            ->name('notifications.read-all');

    });


    /*
    |--------------------------------------------------------------------------
    | Purchase Requisition PDF
    |--------------------------------------------------------------------------
    |
    | MR Preview:
    |
    | /admin/purchase-requisitions/{record}/print
    |
    | PDF:
    |
    | /admin/purchase-requisitions/{record}/print/pdf
    |
    */

    Route::get(
        '/admin/purchase-requisitions/{record}/print/pdf',
        PurchaseRequisitionPdfController::class
    )
        ->middleware('auth')
        ->name('purchase-requisitions.print.pdf');


    Route::get(
        '/purchasing/amr/documents/{document}/download',
        function (
            AssignmentMaterialRequisitionDocument $document
        ) {

            abort_unless(
                Storage::disk('public')->exists(
                    $document->file_path
                ),
                404
            );

            return Storage::disk('public')->download(
                $document->file_path,
                $document->file_name
            );
        }
    )
        ->middleware(['web', 'auth'])
        ->name('purchasing.amr.documents.download');