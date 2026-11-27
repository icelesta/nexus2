<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts\Pages;

use App\Filament\Resources\GoodsReceipts\GoodsReceiptResource;
use App\Models\AssignmentDirectMarket;
use App\Models\DirectMarket;
use App\Models\PurchaseOrder;
use Filament\Resources\Pages\EditRecord;

use App\Livewire\Purchasing\GoodsReceiptItemsGrid;

use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

use App\Services\Purchasing\GoodsReceiptReceivingService;
use Illuminate\Support\Facades\Gate;


class EditGoodsReceipt extends EditRecord
{
    protected static string $resource =
        GoodsReceiptResource::class;

    /*
    |--------------------------------------------------------------------------
    | Page Title
    |--------------------------------------------------------------------------
    */

    public function getTitle(): string
    {
        return 'Edit Receiving Record';
    }

    /*
    |--------------------------------------------------------------------------
    | Page Heading
    |--------------------------------------------------------------------------
    */

    public function getHeading(): string
    {
        return 'Edit Receiving Record';
    }

    /*
    |--------------------------------------------------------------------------
    | Breadcrumb
    |--------------------------------------------------------------------------
    */

    public function getBreadcrumb(): string
    {
        return 'Edit';
    }

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    |
    | Order:
    | 1. Cancel
    | 2. Save Changes
    | 3. Save & Close
    |
    */

    protected function getHeaderActions(): array
    {
        return [

            $this->getCancelFormAction(),

            Action::make('save')
                ->label('Save Changes')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action(function (): void {

                    $this->goodsReceiptSaveIntent = 'stay';

                    $this->save(
                        shouldRedirect: false,
                        shouldSendSavedNotification: false,
                    );

                    $this->dispatch('goods-receipt-save-items');
                }),

            Action::make('saveAndClose')
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->action(function (): void {

                    $this->goodsReceiptSaveIntent = 'close';

                    $this->save(
                        shouldRedirect: false,
                        shouldSendSavedNotification: false,
                    );

                    $this->dispatch('goods-receipt-save-items');
                }),

            Action::make('submitItems')
                ->label('Submit Items')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->visible(fn (): bool =>
                    strtoupper((string) $this->getRecord()->rr_status) === 'DRAFT'
                )
                ->disabled(fn (): bool => ! $this->hasSubmittableItems())
                ->tooltip(fn (): string =>
                    $this->hasSubmittableItems()
                        ? 'Submit all items with positive Receive Qty and QC Passed.'
                        : 'Save at least one item with positive Receive Qty and QC Passed before submitting.'
                )
                ->requiresConfirmation()
                ->modalHeading('Submit Receiving Items')
                ->modalDescription('Only items with positive Receive Qty and QC Passed will be submitted. Other items will remain Outstanding.')
                ->action(function (): void {
                    abort_unless(
                        auth()->user()?->can('Create:GoodsReceipt')
                            || auth()->user()?->can('Update:GoodsReceipt'),
                        403
                    );

                    try {
                        app(GoodsReceiptReceivingService::class)->submitDraft(
                            (int) $this->getRecord()->getKey(),
                            (int) auth()->id()
                        );

                        Notification::make()
                            ->title('Receiving Items submitted successfully.')
                            ->success()
                            ->send();

                        $this->redirect(static::getResource()::getUrl('index'));

                    } catch (\Throwable $e) {
                        report($e);

                        Notification::make()
                            ->title('Unable to submit Receiving Items.')
                            ->body($e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),

        ];
    }

    protected function hasSubmittableItems(): bool
    {
        $record = $this->getRecord();

        if (strtoupper((string) $record->rr_status) !== 'DRAFT') {
            return false;
        }

        return $record->items()
            ->whereNull('deleted_at')
            ->where('draft_receive_qty', '>', 0)
            ->where('qc_passed', true)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Fill Virtual / Display Fields
    |--------------------------------------------------------------------------
    */

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        |
        | supplier_id comes directly from goods_receipts.
        | The form resolves the display label from Supplier Master.
        |
        */

        $supplier = $record->supplier()
            ->with('paymentTerm')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | PAYMENT TERM
        |--------------------------------------------------------------------------
        */

        $data['payment_term_display'] =
            $supplier?->paymentTerm?->term_name;

        /*
        |--------------------------------------------------------------------------
        | RR NUMBER
        |--------------------------------------------------------------------------
        |
        | Existing RR must display its actual generated number.
        | Never generate a new preview during Edit.
        |
        */

        $data['rr_no_preview'] =
            $record->grn_no;

        /*
        |--------------------------------------------------------------------------
        | RECEIVED BY
        |--------------------------------------------------------------------------
        */

        $data['received_by'] =
            \App\Models\User::find($record->created_by)?->name
            ?? '-';

        /*
        |--------------------------------------------------------------------------
        | SOURCE TYPE + SOURCE ID
        |--------------------------------------------------------------------------
        */

        $data['source_type'] = null;
        $data['source_id'] = null;
        $data['currency_display'] = null;

        /*
        |--------------------------------------------------------------------------
        | PURCHASE ORDER
        |--------------------------------------------------------------------------
        */

        if (! blank($record->purchase_order_id)) {

            $purchaseOrder = PurchaseOrder::query()
                ->with('currency')
                ->find($record->purchase_order_id);

            if ($purchaseOrder) {

                $data['source_type'] =
                    'PURCHASE_ORDER';

                $data['source_id'] =
                    $purchaseOrder->getKey();

                $data['currency_display'] =
                    $purchaseOrder->currency?->currency_code;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DIRECT MARKET
        |--------------------------------------------------------------------------
        */

        elseif (! blank($record->assignment_direct_market_id)) {

            $assignment = AssignmentDirectMarket::query()
                ->find($record->assignment_direct_market_id);

            if ($assignment) {

                $data['source_type'] =
                    'ASSIGNMENT_DIRECT_MARKET';

                /*
                | source_id represents AssignmentDirectMarket ID.
                */

                $data['source_id'] =
                    $assignment->getKey();

                /*
                | Currency belongs to Direct Market header.
                */

                if (! blank($assignment->direct_market_id)) {

                    $directMarket = DirectMarket::query()
                        ->with('currency')
                        ->find($assignment->direct_market_id);

                    $data['currency_display'] =
                        $directMarket?->currency?->currency_code;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULT DISPLAY VALUES
        |--------------------------------------------------------------------------
        */

        $data['received_by'] =
            $data['received_by'] ?? '-';

        $data['payment_term_display'] =
            $data['payment_term_display'] ?? null;

        $data['currency_display'] =
            $data['currency_display'] ?? null;

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE PROTECTION
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | Edit RR may ONLY modify:
    |
    | - document_number
    | - remarks
    |
    | All other transaction identity fields are locked.
    |
    */

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return [
            'document_number' =>
                filled($data['document_number'] ?? null)
                    ? trim((string) $data['document_number'])
                    : null,

            'remarks' =>
                filled($data['remarks'] ?? null)
                    ? trim((string) $data['remarks'])
                    : null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Saved Notification
    |--------------------------------------------------------------------------
    */

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Receiving Record updated successfully.';
    }

    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    /*
    |--------------------------------------------------------------------------
    | Content
    |--------------------------------------------------------------------------
    */

    public function content(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([

                Group::make([
                    EmbeddedSchema::make('form'),
                ]),

                /*
                |--------------------------------------------------------------------------
                | FORM ACTIONS
                |--------------------------------------------------------------------------
                |
                | Intentionally empty.
                | Actions are now located in the page Header.
                |
                */

                Group::make([
                    $this->getFormActionsContentComponent(),
                ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | GOODS RECEIPT ITEMS GRID
                |--------------------------------------------------------------------------
                */

                Livewire::make(
                    GoodsReceiptItemsGrid::class
                )
                    ->key(
                        'goods-receipt-items-grid-' .
                        $this->record->getKey()
                    )
                    ->data([
                        'goodsReceipt' => $this->getRecord(),
                    ])
                    ->columnSpanFull(),

            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Form Actions
    |--------------------------------------------------------------------------
    |
    | No bottom form actions.
    | Save / Cancel actions are handled in Header.
    |
    */

    protected function getFormActions(): array
    {
        return [];
    }

    public string $goodsReceiptSaveIntent = 'stay';

    #[On('goods-receipt-items-saved')]
    public function handleGoodsReceiptItemsSaved(): void
    {
        Notification::make()
            ->title('Receiving Record saved successfully.')
            ->success()
            ->send();

        if ($this->goodsReceiptSaveIntent === 'close') {
            $this->goodsReceiptSaveIntent = 'stay';

            $this->redirect(static::getResource()::getUrl('index'));

            return;
        }

        $this->goodsReceiptSaveIntent = 'stay';
    }

    #[On('goods-receipt-qc-updated')]
    public function refreshSubmitState(): void
    {
        $this->getRecord()->refresh();
    }

}