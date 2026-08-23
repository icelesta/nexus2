<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ApprovalTransaction;
use App\Models\PurchaseRequisition;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PurchaseRequisitionPdfController extends Controller
{
    public function __invoke(int|string $record): Response
    {
        /*
        |--------------------------------------------------------------------------
        | LOAD MATERIAL REQUISITION
        |--------------------------------------------------------------------------
        */

        $purchaseRequisition = PurchaseRequisition::query()
            ->with([
                'company',
                'businessUnit',
                'branch',
                'department',
                'section',
                'costCenter',
                'warehouse',

                'requester',
                'createdBy',

                'items.item',
                'items.uom',
                'items.warehouse',

                /*
                |--------------------------------------------------------------------------
                | AMR COMMERCIAL SYNCHRONIZATION
                |--------------------------------------------------------------------------
                */

                'items.latestAssignmentMaterialRequisitionItem.supplier',
            ])
            ->findOrFail($record);


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT INFORMATION
        |--------------------------------------------------------------------------
        */

        $documentNumber = (string) (
            $purchaseRequisition->pr_no
            ?? 'MR'
        );


        $documentDate = $purchaseRequisition->request_date
            ? \Carbon\Carbon::parse(
                $purchaseRequisition->request_date
            )->format('d M Y')
            : '-';


        $documentStatus = strtoupper(
            (string) (
                $purchaseRequisition->status
                ?? '-'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | APPROVAL SIGNATURE DATA
        |--------------------------------------------------------------------------
        |
        | Approval Engine is the single source of truth.
        |
        | Requested By
        |     → Approval Transaction Creator
        |     → submitted_at
        |
        | Approved By #1
        |     → Approval Level 1
        |     → approver
        |     → acted_at
        |
        | Approved By #2
        |     → Approval Level 2
        |     → approver
        |     → acted_at
        |
        |--------------------------------------------------------------------------
        */

        $approvalTransaction =
            ApprovalTransaction::query()
                ->with([
                    'creator',
                    'steps.approver',
                ])
                ->where(
                    'document_type',
                    'MATERIAL_REQUISITION'
                )
                ->where(
                    'document_id',
                    $purchaseRequisition->getKey()
                )
                ->latest('id')
                ->first();


        /*
        |--------------------------------------------------------------------------
        | APPROVAL LEVEL 1
        |--------------------------------------------------------------------------
        */

        $approvalStep1 = $approvalTransaction
            ?->steps
            ->first(
                fn ($step) =>
                    (int) $step->approval_level === 1
                    && $step->status === 'APPROVED'
            );


        /*
        |--------------------------------------------------------------------------
        | APPROVAL LEVEL 2
        |--------------------------------------------------------------------------
        */

        $approvalStep2 = $approvalTransaction
            ?->steps
            ->first(
                fn ($step) =>
                    (int) $step->approval_level === 2
                    && $step->status === 'APPROVED'
            );


        /*
        |--------------------------------------------------------------------------
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'filament.resources.purchase-requisitions.pages.purchase-requisition-pdf',
            [

                /*
                |--------------------------------------------------------------------------
                | CORE RECORD
                |--------------------------------------------------------------------------
                */

                'record' =>
                    $purchaseRequisition,


                /*
                |--------------------------------------------------------------------------
                | DOCUMENT INFORMATION
                |--------------------------------------------------------------------------
                */

                'documentNumber' =>
                    $documentNumber,

                'documentDate' =>
                    $documentDate,

                'documentStatus' =>
                    $documentStatus,


                /*
                |--------------------------------------------------------------------------
                | APPROVAL SIGNATURE DATA
                |--------------------------------------------------------------------------
                */

                'approvalTransaction' =>
                    $approvalTransaction,

                'approvalStep1' =>
                    $approvalStep1,

                'approvalStep2' =>
                    $approvalStep2,

            ]
        );


        /*
        |--------------------------------------------------------------------------
        | PDF PAPER
        |--------------------------------------------------------------------------
        */

        $pdf->setPaper(
            'a4',
            'portrait'
        );


        /*
        |--------------------------------------------------------------------------
        | DOMPDF OPTIONS
        |--------------------------------------------------------------------------
        */

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'isPhpEnabled' => false,
            'defaultFont' => 'DejaVu Sans',
        ]);


        /*
        |--------------------------------------------------------------------------
        | PDF FILENAME
        |--------------------------------------------------------------------------
        */

        $filename = preg_replace(
            '/[^\pL\pN\-_\.]+/u',
            '-',
            $documentNumber
        ) ?? 'material-requisition';


        $filename = trim(
            $filename,
            '-'
        );


        if ($filename === '') {
            $filename = 'material-requisition';
        }


        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD
        |--------------------------------------------------------------------------
        */

        return $pdf->download(
            $filename . '.pdf'
        );
    }
}