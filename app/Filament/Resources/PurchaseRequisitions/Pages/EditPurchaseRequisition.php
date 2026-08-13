<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Pages;

use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use App\Services\Purchasing\PurchaseRequisitionService;
use App\Services\Purchasing\PurchaseRequisitionItemService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditPurchaseRequisition extends EditRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;

    /**
     * Item currently being edited inline.
     */
    public ?int $editingItemId = null;

    /**
     * Enter inline edit mode.
     */
    public function editItem(int $itemId): void
    {
        $item = $this->record
            ->items
            ->firstWhere('id', $itemId);

        abort_if(! $item, 404);

        $this->editingItemId = $itemId;

        $this->editingData = [
            'quantity' => (float) $item->quantity,
            'remarks'  => $item->remarks,
        ];
    }

    /**
     * Exit inline edit mode.
     */
    public function cancelEditItem(): void
    {
        $this->editingItemId = null;
    }

    public function saveItem(): void
    {
        app(
            PurchaseRequisitionItemService::class
        )->updateItem(
            $this->editingItemId,
            [
                'quantity' => $this->editingData['quantity'],
                'remarks'  => $this->editingData['remarks'],
            ]
        );

        $this->record->refresh()->load([
            'items.item',
            'items.uom',
            'items.warehouse',
        ]);

        $this->editingItemId = null;
        $this->editingData = [];
    }

    public function deleteItem(
        int $itemId,
    ): void {

        app(
            PurchaseRequisitionItemService::class
        )->deleteItem($itemId);

        $this->record->refresh()->load([
            'items.item',
            'items.uom',
            'items.warehouse',
        ]);
    }


    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->action(function () {

                    app(
                        PurchaseRequisitionService::class
                    )->delete(
                        $this->record->id
                    );

                    $this->redirect(
                        static::getResource()::getUrl('index')
                    );

                }),
        ];
    }

    public array $editingData = [];

    public function getTitle(): string
    {
        return $this->record->pr_no;
    }

    public function getHeading(): string
    {
        return 'Material Requisition';
    }

    public function getSubheading(): string|HtmlString|null
    {
        $status = $this->record->status ?? 'Draft';

        $colors = [
            'Draft'            => '#f59e0b',
            'Submitted'        => '#3b82f6',
            'Pending Approval' => '#f97316',
            'Approved'         => '#22c55e',
            'Rejected'         => '#ef4444',
            'Cancelled'        => '#dc2626',
            'Closed'           => '#6b7280',
        ];

        $color = $colors[$status] ?? '#6b7280';

        return new HtmlString("
            <div style='display:flex;justify-content:space-between;align-items:center;width:100%;'>
                <span>{$this->record->pr_no}</span>

                <span style='
                    background: {$color};
                    color:#fff;
                    padding:4px 12px;
                    border-radius:999px;
                    font-size:12px;
                    font-weight:600;
                    letter-spacing:.5px;
                '>
                    {$status}
                </span>
            </div>
        ");
    }

    public function getBreadcrumb(): string
    {
        return 'Edit';
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Material Requisition updated successfully.';
    }

    public function clearItems(): void
    {
        app(
            PurchaseRequisitionItemService::class
        )->clearItems(
            $this->record
        );

        $this->record->refresh();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }   

    protected function handleRecordUpdate(
        \Illuminate\Database\Eloquent\Model $record,
        array $data,
    ): \Illuminate\Database\Eloquent\Model
    {
        return app(
            PurchaseRequisitionService::class
        )->update(
            $record->getKey(),
            $data,
        );
    }     

}