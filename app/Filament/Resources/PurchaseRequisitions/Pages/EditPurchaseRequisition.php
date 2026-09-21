<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Pages;

use App\Filament\Resources\PurchaseRequisitions\Actions\ApprovePurchaseRequisition;
use App\Filament\Resources\PurchaseRequisitions\Actions\RejectPurchaseRequisition;

use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use App\Services\Purchasing\PurchaseRequisitionService;
use App\Services\Purchasing\PurchaseRequisitionItemService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

use App\Models\ApprovalTransaction;
use App\Models\PurchaseRequisition;

use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

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
        if (
            $this->record->status !== PurchaseRequisition::STATUS_DRAFT
            && ! $this->isLevelOneApprovalEdit()
        ) {
            abort(403);
        }

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
        $item = $this->record
            ->items
            ->firstWhere('id', $this->editingItemId);

        abort_if(! $item, 404);

        if (! is_numeric($this->editingData['quantity'] ?? null)) {
            Notification::make()
                ->danger()
                ->title('Invalid Quantity')
                ->body('Please enter a number.')
                ->send();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Approval Level 1 Restricted Edit
        |--------------------------------------------------------------------------
        */

        if ($this->isLevelOneApprovalEdit()) {

            $newQuantity = (float) $this->editingData['quantity'];
            $currentQuantity = (float) $item->quantity;

            if ($newQuantity >= $currentQuantity) {
                Notification::make()
                    ->danger()
                    ->title('Quantity Reduction Required')
                    ->body(
                        'During Level 1 approval, quantity can only be reduced. '
                        . 'The new quantity must be lower than the current quantity.'
                    )
                    ->send();

                return;
            }

            app(PurchaseRequisitionItemService::class)
                ->updateItem(
                    $item->id,
                    [
                        'quantity' => $newQuantity,
                    ]
                );

        } else {

            /*
            |--------------------------------------------------------------------------
            | Existing Draft Behavior
            |--------------------------------------------------------------------------
            */

            app(PurchaseRequisitionItemService::class)
                ->updateItem(
                    $item->id,
                    [
                        'quantity' => $this->editingData['quantity'],
                        'remarks'  => $this->editingData['remarks'],
                    ]
                );
        }

        $this->record->refresh()->load([
            'items.item',
            'items.uom',
            'items.warehouse',
        ]);

        $this->editingItemId = null;
        $this->editingData = [];
    }

    public function deleteItem(int $itemId): void
    {
        if ($this->isLevelOneApprovalEdit()) {
            Notification::make()
                ->danger()
                ->title('Action Not Allowed')
                ->body(
                    'Items cannot be deleted while the Material Requisition is awaiting Level 1 approval.'
                )
                ->send();

            return;
        }

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

            /*
            |--------------------------------------------------------------------------
            | APPROVE
            |--------------------------------------------------------------------------
            */

            ApprovePurchaseRequisition::make(),

            /*
            |--------------------------------------------------------------------------
            | REJECT
            |--------------------------------------------------------------------------
            */

            RejectPurchaseRequisition::make(),

            /*
            |--------------------------------------------------------------------------
            | SAVE
            |--------------------------------------------------------------------------
            */

            $this->getSaveFormAction()
                ->formId('form')
                ->visible(
                    fn (): bool =>
                        ! $this->isLevelOneApprovalEdit()
                ),

            /*
            |--------------------------------------------------------------------------
            | CANCEL
            |--------------------------------------------------------------------------
            */

            $this->getCancelFormAction(),

            /*
            |--------------------------------------------------------------------------
            | DELETE
            |--------------------------------------------------------------------------
            */

            DeleteAction::make()
                ->requiresConfirmation()
                ->disabled(
                    fn (): bool =>
                        $this->record->status !==
                        \App\Models\PurchaseRequisition::STATUS_DRAFT
                )
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

    protected function getFormActions(): array
    {
        return [];
    }


    protected function getSavedNotificationTitle(): ?string
    {
        return 'Material Requisition updated successfully.';
    }

    public function clearItems(): void
    {
        if ($this->isLevelOneApprovalEdit()) {
            Notification::make()
                ->danger()
                ->title('Action Not Allowed')
                ->body(
                    'Items cannot be cleared while the Material Requisition is awaiting Level 1 approval.'
                )
                ->send();

            return;
        }

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
        if ($this->isLevelOneApprovalEdit()) {
            Notification::make()
                ->danger()
                ->title('Action Not Allowed')
                ->body(
                    'Only quantity reduction is allowed while the Material Requisition is awaiting Level 1 approval.'
                )
                ->send();

            throw ValidationException::withMessages([
                'record' => 'Only quantity reduction is allowed during Level 1 approval.',
            ]);
        }

        return app(
            PurchaseRequisitionService::class
        )->update(
            $record->getKey(),
            $data,
        );
    }

    public function isLevelOneApprovalEdit(): bool
    {
        if (
            ! $this->record
            || $this->record->status !== PurchaseRequisition::STATUS_WAITING_APPROVAL
        ) {
            return false;
        }

        $user = auth()->user();

        if (! $user) {
            return false;
        }

        $transaction = ApprovalTransaction::query()
            ->where('document_type', 'MATERIAL_REQUISITION')
            ->where('document_id', $this->record->getKey())
            ->where('status', 'PENDING')
            ->latest('id')
            ->first();

        if (! $transaction || (int) $transaction->current_level !== 1) {
            return false;
        }

        $step = $transaction->steps()
            ->where('approval_level', 1)
            ->where('status', 'PENDING')
            ->first();

        if (! $step || ! $step->role_id) {
            return false;
        }

        return $user->roles()
            ->where('roles.id', $step->role_id)
            ->where('roles.guard_name', 'web')
            ->where('roles.is_active', true)
            ->exists();
    }    


}