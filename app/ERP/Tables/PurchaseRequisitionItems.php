<?php

declare(strict_types=1);

namespace App\ERP\Tables;


use App\ERP\DTO\DocumentToolbarAction;

/**
 * Purchase Requisition Items
 *
 * First implementation of the ERPDocumentItemTable engine
 * for the Purchasing module.
 *
 * This class is responsible only for configuring the
 * Purchase Requisition Item Grid.
 *
 * It contains no business logic, database queries,
 * or document workflow.
 */
class PurchaseRequisitionItems extends ERPDocumentItemTable
{
    /**
     * Document title.
     */
    public function getTitle(): string
    {
        return 'Purchase Requisition Items';
    }

    /**
     * Document description.
     */
    public function getDescription(): ?string
    {
        return 'List of requested items.';
    }

    /**
     * Toolbar actions.
     *
     * @return array<int, mixed>
     */
    public function getToolbarActions(): array
    {
        return [

            DocumentToolbarAction::make('add_item')
                ->label('Add Item')
                ->icon('heroicon-o-plus'),

            DocumentToolbarAction::make('import_excel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray'),

            DocumentToolbarAction::make('copy_from')
                ->label('Copy From')
                ->icon('heroicon-o-document-duplicate'),

            DocumentToolbarAction::make('clear')
                ->label('Clear')
                ->icon('heroicon-o-trash')
                ->color(DocumentToolbarAction::COLOR_DANGER),

        ];
    }

    /**
     * Grid columns.
     *
     * @return array<int, mixed>
     */
    public function getColumns(): array
    {
        return [
            [
                'field' => 'line_no',
                'label' => 'No',
            ],
            [
                'field' => 'item_code',
                'label' => 'Item Code',
            ],
            [
                'field' => 'item_name',
                'label' => 'Item Name',
            ],
            [
                'field' => 'description',
                'label' => 'Description',
            ],
            [
                'field' => 'qty',
                'label' => 'Qty',
            ],
            [
                'field' => 'uom',
                'label' => 'UOM',
            ],
            [
                'field' => 'required_date',
                'label' => 'Required Date',
            ],
            [
                'field' => 'warehouse',
                'label' => 'Warehouse',
            ],
            [
                'field' => 'status',
                'label' => 'Status',
            ],
        ];
    }

    /**
     * Row actions.
     *
     * @return array<int, mixed>
     */
    public function getRowActions(): array
    {
        return [
            [
                'name' => 'edit',
                'label' => 'Edit',
                'icon' => 'heroicon-o-pencil-square',
            ],
            [
                'name' => 'delete',
                'label' => 'Delete',
                'icon' => 'heroicon-o-trash',
                'color' => 'danger',
            ],
        ];
    }

    /**
     * Footer summary.
     *
     * @return array<int, mixed>
     */
    public function getSummary(): array
    {
        return [
            [
                'key' => 'total_lines',
                'label' => 'Total Lines',
            ],
            [
                'key' => 'total_quantity',
                'label' => 'Total Quantity',
            ],
        ];
    }

    /**
     * Empty state heading.
     */
    public function getEmptyStateHeading(): string
    {
        return 'No Purchase Requisition Items';
    }

    /**
     * Empty state description.
     */
    public function getEmptyStateDescription(): ?string
    {
        return 'Click "Add Item" to create the first purchase requisition item.';
    }
}