<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Purchasing;

use Filament\Widgets\Widget;

class PurchasingWorkflowPipeline extends Widget
{
    /**
     * Blade view yang digunakan widget.
     */
    protected string $view = 'filament.widgets.purchasing.workflow-pipeline';

    /**
     * Span widget pada dashboard.
     */
    protected int|string|array $columnSpan = 'full';

    /**
     * Data workflow pipeline.
     */
    public function getWorkflowStages(): array
    {
        return [
            [
                'title'  => 'Material Requisition',
                'code'   => 'MR',
                'count'  => 0,
                'status' => 'Open',
                'icon'   => 'heroicon-o-document-text',
                'color'  => 'primary',
            ],

            [
                'title'  => 'Assignment',
                'code'   => 'AMR',
                'count'  => 0,
                'status' => 'Pending',
                'icon'   => 'heroicon-o-user-circle',
                'color'  => 'warning',
            ],

            [
                'title'  => 'Purchase Order',
                'code'   => 'PO',
                'count'  => 0,
                'status' => 'Draft',
                'icon'   => 'heroicon-o-shopping-cart',
                'color'  => 'success',
            ],

            [
                'title'  => 'Goods Receipt',
                'code'   => 'GR',
                'count'  => 0,
                'status' => 'Waiting',
                'icon'   => 'heroicon-o-archive-box',
                'color'  => 'info',
            ],

            [
                'title'  => 'Vendor Invoice',
                'code'   => 'INV',
                'count'  => 0,
                'status' => 'Pending',
                'icon'   => 'heroicon-o-receipt-percent',
                'color'  => 'gray',
            ],

            [
                'title'  => 'Payment',
                'code'   => 'PAY',
                'count'  => 0,
                'status' => 'Waiting',
                'icon'   => 'heroicon-o-banknotes',
                'color'  => 'success',
            ],
        ];
    }
}