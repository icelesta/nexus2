<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource\PurchaseOrderResource;
use App\Livewire\Purchasing\PurchaseOrderItemsGrid;
use App\Models\ApprovalTransaction;
use App\Models\PurchaseOrder;
use App\Services\Approval\ApprovalTransactionService;
use App\Services\Purchasing\PurchaseOrderService;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\View;

use Illuminate\Database\Eloquent\Model;
use Throwable;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource =
        PurchaseOrderResource::class;

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Save Changes
            |--------------------------------------------------------------------------
            */

            Action::make('save')
                ->label('Save Changes')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save')
                ->visible(
                    fn (): bool => $this->record->canEdit()
                ),

            /*
            |--------------------------------------------------------------------------
            | Save & Close
            |--------------------------------------------------------------------------
            */

            Action::make('saveAndClose')
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->action(function (): void {

                    $this->save(
                        shouldRedirect: false,
                        shouldSendSavedNotification: true,
                    );

                    $this->redirect(
                        static::getResource()::getUrl('index')
                    );

                })
                ->visible(
                    fn (): bool => $this->record->canEdit()
                ),

            /*
            |--------------------------------------------------------------------------
            | Submit
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | Existing PurchaseOrder submit workflow remains untouched.
            |
            */

            Action::make('submit')
                ->label('Submit')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Submit Purchase Order')
                ->modalDescription(
                    'This Purchase Order will be submitted for approval.'
                )
                ->action('submit')
                ->visible(
                    fn (): bool => $this->record->canSubmit()
                ),


            /*
            |--------------------------------------------------------------------------
            | Preview
            |--------------------------------------------------------------------------
            */

            Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(
                    fn (): string => route(
                        'purchase-orders.preview',
                        $this->record
                    )
                )
                ->openUrlInNewTab(),

            /*
            |--------------------------------------------------------------------------
            | Print
            |--------------------------------------------------------------------------
            */

            Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(
                    fn (): string => route(
                        'purchase-orders.print',
                        $this->record
                    )
                )
                ->openUrlInNewTab(),

            /*
            |--------------------------------------------------------------------------
            | Approve Purchase Order
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | The approver is NOT hard-coded.
            |
            | ApprovalTransactionService::canApprove()
            | determines whether the current user is authorized
            | based on the Approval Master transaction snapshot.
            |
            |--------------------------------------------------------------------------
            */

            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(
                    'Approve Purchase Order?'
                )
                ->modalDescription(
                    fn (): string =>
                        "Approve Purchase Order {$this->record->document_no}?"
                )
                ->modalSubmitActionLabel(
                    'Yes, Approve'
                )
                ->visible(
                    fn (): bool =>
                        $this->canCurrentUserApprove()
                )
                ->action(
                    function (): void {

                        try {

                            $transaction =
                                $this->getApprovalTransaction();

                            if (! $transaction) {
                                throw new \RuntimeException(
                                    'Purchase Order approval transaction was not found.'
                                );
                            }

                            app(
                                ApprovalTransactionService::class
                            )->approve(
                                $transaction,
                                auth()->user(),
                            );

                            Notification::make()
                                ->success()
                                ->title(
                                    'Purchase Order Approved'
                                )
                                ->body(
                                    "Purchase Order {$this->record->document_no} has been approved."
                                )
                                ->send();

                            $this->redirect(
                                static::getResource()::getUrl(
                                    'index'
                                )
                            );

                        } catch (Throwable $exception) {

                            Notification::make()
                                ->danger()
                                ->title(
                                    'Unable to Approve Purchase Order'
                                )
                                ->body(
                                    $exception->getMessage()
                                )
                                ->persistent()
                                ->send();
                        }
                    }
                ),

            /*
            |--------------------------------------------------------------------------
            | Reject Purchase Order
            |--------------------------------------------------------------------------
            */

            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(
                    'Reject Purchase Order?'
                )
                ->modalDescription(
                    fn (): string =>
                        "Reject Purchase Order {$this->record->document_no}?"
                )
                ->modalSubmitActionLabel(
                    'Yes, Reject'
                )
                ->form([
                    Textarea::make('remarks')
                        ->label('Remarks')
                        ->placeholder(
                            'Enter rejection reason...'
                        )
                        ->required()
                        ->rows(4),
                ])
                ->visible(
                    fn (): bool =>
                        $this->canCurrentUserApprove()
                )
                ->action(
                    function (
                        array $data
                    ): void {

                        try {

                            $transaction =
                                $this->getApprovalTransaction();

                            if (! $transaction) {
                                throw new \RuntimeException(
                                    'Purchase Order approval transaction was not found.'
                                );
                            }

                            app(
                                ApprovalTransactionService::class
                            )->reject(
                                $transaction,
                                auth()->user(),
                                $data['remarks'] ?? null,
                            );

                            Notification::make()
                                ->success()
                                ->title(
                                    'Purchase Order Rejected'
                                )
                                ->body(
                                    "Purchase Order {$this->record->document_no} has been rejected."
                                )
                                ->send();

                            $this->redirect(
                                static::getResource()::getUrl(
                                    'index'
                                )
                            );

                        } catch (Throwable $exception) {

                            Notification::make()
                                ->danger()
                                ->title(
                                    'Unable to Reject Purchase Order'
                                )
                                ->body(
                                    $exception->getMessage()
                                )
                                ->persistent()
                                ->send();
                        }
                    }
                ),

            /*
            |--------------------------------------------------------------------------
            | Delete
            |--------------------------------------------------------------------------
            */

            DeleteAction::make()
                ->visible(
                    fn (): bool =>
                        $this->record->canDelete()
                ),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Form Actions
    |--------------------------------------------------------------------------
    */

    protected function getFormActions(): array
    {
        return [];
    }


    public function submit(): void
    {
        try {

            $this->record = app(
                PurchaseOrderService::class
            )->submit(
                $this->record
            );

            Notification::make()
                ->success()
                ->title('Purchase Order Submitted')
                ->body(
                    "Purchase Order {$this->record->document_no} has been submitted for approval."
                )
                ->send();

            $this->redirect(
                static::getResource()::getUrl('index')
            );

        } catch (Throwable $exception) {

            Notification::make()
                ->danger()
                ->title('Unable to Submit Purchase Order')
                ->body(
                    $exception->getMessage()
                )
                ->persistent()
                ->send();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Content
    |--------------------------------------------------------------------------
    */

    public function content(
        Schema $schema
    ): Schema {

        return $schema
            ->columns(1)
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Document Header
                |--------------------------------------------------------------------------
                */

                View::make(
                    'filament.resources.purchase-orders.pages.partials.document-header'
                )
                    ->viewData([
                        'record' => $this->record,
                    ])
                    ->extraAttributes([
                        'class' => 'po-document-header-container',
                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Header Form
                |--------------------------------------------------------------------------
                */

                Group::make([

                    EmbeddedSchema::make('form'),

                ]),

                /*
                |--------------------------------------------------------------------------
                | Form Actions
                |--------------------------------------------------------------------------
                */

                Group::make([

                    $this->getFormActionsContentComponent(),

                ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Purchase Order Items
                |--------------------------------------------------------------------------
                */

                Livewire::make(
                    PurchaseOrderItemsGrid::class
                )
                    ->key(
                        'purchase-order-items-grid'
                    )
                    ->data([
                        'purchaseOrder' => $this->record,
                        'readonly'      => false,
                    ])
                    ->columnSpanFull(),

            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PO Approval Transaction
    |--------------------------------------------------------------------------
    */

    protected function getApprovalTransaction():
        ?ApprovalTransaction {

        return ApprovalTransaction::query()
            ->where(
                'document_type',
                'PURCHASE_ORDER'
            )
            ->where(
                'document_id',
                (int) $this->record->getKey()
            )
            ->latest('id')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Current User Approval Permission
    |--------------------------------------------------------------------------
    */

    protected function canCurrentUserApprove(): bool
    {
        if (
            ! auth()->check()
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | PO Must Be Waiting For Approval
        |--------------------------------------------------------------------------
        */

        if (
            $this->record->approval_status
            !== PurchaseOrder::APPROVAL_WAITING
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | PO Must Be Submitted
        |--------------------------------------------------------------------------
        */

        if (
            $this->record->status
            !== PurchaseOrder::STATUS_SUBMIT
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Get PO Approval Transaction
        |--------------------------------------------------------------------------
        */

        $transaction =
            $this->getApprovalTransaction();

        if (! $transaction) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Approval Engine Determines Approver
        |--------------------------------------------------------------------------
        */

        return app(
            ApprovalTransactionService::class
        )->canApprove(
            $transaction,
            auth()->user()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Utility
    |--------------------------------------------------------------------------
    */

    protected function refreshRecord(): void
    {
        $this->record->refresh();
    }

    protected function success(
        string $message
    ): void {

        Notification::make()
            ->success()
            ->title($message)
            ->send();
    }

    /*
    |--------------------------------------------------------------------------
    | Record Update
    |--------------------------------------------------------------------------
    */

    protected function handleRecordUpdate(
        Model $record,
        array $data,
    ): Model {

        $data['updated_by'] =
            auth()->id();

        $record->update(
            $data
        );

        return $record;
    }

    /*
    |--------------------------------------------------------------------------
    | Document Header
    |--------------------------------------------------------------------------
    */

    protected function getDocumentHeader(): array
    {
        return [

            'number' =>
                $this->record->document_no,

            'date' =>
                $this->record->document_date,

            'status' =>
                $this->record->status,

        ];
    }
}