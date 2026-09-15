<?php

declare(strict_types=1);

namespace App\Filament\Resources\GeneratePurchaseOrderResource\Pages;

use App\Filament\Resources\GeneratePurchaseOrderResource\GeneratePurchaseOrderResource;
use App\Models\ApprovalTransaction;
use App\Services\Approval\ApprovalTransactionService;
use App\Services\Purchasing\GeneratePurchaseOrderEligibilityService;
use App\Services\Purchasing\GeneratePurchaseOrderService;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

use Throwable;

class EditGeneratePurchaseOrder extends EditRecord
{
    protected static string $resource =
        GeneratePurchaseOrderResource::class;

    /*
    |--------------------------------------------------------------------------
    | Disable Record Editing
    |--------------------------------------------------------------------------
    */

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {

        return [];
    }

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
            | Approve
            |--------------------------------------------------------------------------
            */

            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')

                ->visible(
                    fn (): bool =>
                        $this->canCurrentUserApprove()
                )

                ->requiresConfirmation()

                ->modalHeading(
                    'Approve Current Level'
                )

                ->modalDescription(
                    'This will approve the current approval level using the Nexus Approval Engine.'
                )

                ->action(function (): void {
                    $this->approveCurrentLevel();
                }),

            /*
            |--------------------------------------------------------------------------
            | Reject
            |--------------------------------------------------------------------------
            */

            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')

                ->visible(
                    fn (): bool =>
                        $this->canCurrentUserApprove()
                )

                ->requiresConfirmation()

                ->modalHeading(
                    'Reject Current Approval'
                )

                ->action(
                    function (
                        array $data
                    ): void {

                        $this->rejectCurrentLevel(
                            $data['remarks'] ?? null
                        );
                    }
                )

                ->form([

                    \Filament\Forms\Components\Textarea::make(
                        'remarks'
                    )
                        ->label('Remarks')
                        ->required()
                        ->rows(4),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Generate PO
            |--------------------------------------------------------------------------
            */

            Action::make('generatePo')
                ->label('Generate PO')
                ->icon('heroicon-o-document-plus')
                ->color('primary')

                ->visible(
                    fn (): bool =>
                        $this->canGeneratePurchaseOrder()
                )

                ->disabled(
                    fn (): bool =>
                        ! $this->canGeneratePurchaseOrder()
                )

                ->requiresConfirmation()

                ->modalHeading(
                    'Generate Purchase Order'
                )

                ->modalDescription(
                    'The Purchase Order will be generated from the approved Assignment Material Requisition snapshot.'
                )

                ->action(function (): void {
                    $this->generatePurchaseOrder();
                }),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Approval Transaction
    |--------------------------------------------------------------------------
    */

    protected function getApprovalTransaction():
        ?ApprovalTransaction
    {
        return ApprovalTransaction::query()
            ->with([
                'approvalMaster',
                'steps',
            ])
            ->where(
                'document_type',
                'MATERIAL_REQUISITION'
            )
            ->where(
                'document_id',
                $this->record->purchase_requisition_id
            )
            ->latest('id')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Can Approve
    |--------------------------------------------------------------------------
    */

    protected function canCurrentUserApprove(): bool
    {
        $transaction =
            $this->getApprovalTransaction();

        $user = auth()->user();

        if (
            ! $transaction
            || ! $user
        ) {
            return false;
        }

        return app(
            ApprovalTransactionService::class
        )->canApprove(
            $transaction,
            $user
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Approve
    |--------------------------------------------------------------------------
    */

    protected function approveCurrentLevel(): void
    {
        try {

            $transaction =
                $this->getApprovalTransaction();

            $user = auth()->user();

            if (
                ! $transaction
                || ! $user
            ) {
                throw new \RuntimeException(
                    'Approval transaction or authenticated user not found.'
                );
            }

            $service = app(
                ApprovalTransactionService::class
            );

            if (
                ! $service->canApprove(
                    $transaction,
                    $user
                )
            ) {
                throw new \RuntimeException(
                    'You are not authorized to approve the current approval level.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Execute Approval
            |--------------------------------------------------------------------------
            */

            $service->approve(
                $transaction,
                $user
            );

            /*
            |--------------------------------------------------------------------------
            | Refresh Transaction State
            |--------------------------------------------------------------------------
            */

            $this->record->refresh();

            $this->refreshFormData([
                'status',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Force Dynamic Workbench Refresh
            |--------------------------------------------------------------------------
            */

            $this->dispatch('$refresh');

            Notification::make()
                ->success()
                ->title('Approval Completed')
                ->body(
                    'The current approval level has been processed successfully.'
                )
                ->send();

        } catch (Throwable $exception) {

            Notification::make()
                ->danger()
                ->title('Approval Failed')
                ->body(
                    $exception->getMessage()
                )
                ->send();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Reject
    |--------------------------------------------------------------------------
    */

    protected function rejectCurrentLevel(
        ?string $remarks
    ): void {

        try {

            $transaction =
                $this->getApprovalTransaction();

            $user = auth()->user();

            if (
                ! $transaction
                || ! $user
            ) {
                throw new \RuntimeException(
                    'Approval transaction or authenticated user not found.'
                );
            }

            $service = app(
                ApprovalTransactionService::class
            );

            $service->reject(
                $transaction,
                $user,
                $remarks
            );

            Notification::make()
                ->success()
                ->title('Transaction Rejected')
                ->body(
                    'The current approval transaction has been rejected.'
                )
                ->send();

            $this->redirect(
                GeneratePurchaseOrderResource::getUrl(
                    'index'
                )
            );

        } catch (Throwable $exception) {

            Notification::make()
                ->danger()
                ->title('Rejection Failed')
                ->body(
                    $exception->getMessage()
                )
                ->send();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Generate PO Eligibility
    |--------------------------------------------------------------------------
    */

    protected function canGeneratePurchaseOrder(): bool
    {
        if (auth()->user()?->can('Update:GeneratePurchaseOrder') !== true) {
            return false;
        }

        $this->record->refresh();

        return app(
            GeneratePurchaseOrderEligibilityService::class
        )->canGenerate(
            $this->record
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Generate PO
    |--------------------------------------------------------------------------
    */

    protected function generatePurchaseOrder(): void
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Refresh Current Assignment
            |--------------------------------------------------------------------------
            |
            | The workbench is dynamic. Always read the latest database state
            | before executing the final server-side gate.
            |
            */

            $this->record->refresh();

            /*
            |--------------------------------------------------------------------------
            | Server-Side Authorization Gate
            |--------------------------------------------------------------------------
            */

            abort_unless(
                auth()->user()?->can('Update:GeneratePurchaseOrder') === true,
                403,
            );

            /*
            |--------------------------------------------------------------------------
            | Server-Side Business Eligibility Gate
            |--------------------------------------------------------------------------
            */

            app(
                GeneratePurchaseOrderEligibilityService::class
            )->assertEligible(
                $this->record
            );

            /*
            |--------------------------------------------------------------------------
            | Generate Purchase Order
            |--------------------------------------------------------------------------
            */

            $purchaseOrders =
                app(
                    GeneratePurchaseOrderService::class
                )->generate(
                    (int) $this->record->getKey()
                );

            /*
            |--------------------------------------------------------------------------
            | Success Notification
            |--------------------------------------------------------------------------
            */

            $purchaseOrderCount = count($purchaseOrders);

            $purchaseOrderNumbers = collect($purchaseOrders)
                ->pluck('document_no')
                ->implode(', ');

            Notification::make()
                ->success()
                ->title('Purchase Order Generated')
                ->body(
                    "{$purchaseOrderCount} Purchase Order(s) generated successfully: {$purchaseOrderNumbers}."
                )
                ->send();

            /*
            |--------------------------------------------------------------------------
            | Redirect to Purchase Order List
            |--------------------------------------------------------------------------
            |
            | Point 9 requirement:
            |
            | Generate PO
            |      ↓
            | PO Draft
            |      ↓
            | PO List
            |
            */

            $this->redirect(
                \App\Filament\Resources\PurchaseOrderResource\PurchaseOrderResource::getUrl(
                    'index'
                )
            );

        } catch (Throwable $exception) {

            Notification::make()
                ->danger()
                ->title('Unable to Generate Purchase Order')
                ->body(
                    $exception->getMessage()
                )
                ->send();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Page
    |--------------------------------------------------------------------------
    */

    public function getTitle(): string
    {
        return $this->record->document_no
            ?? 'Generate Purchase Order';
    }

    public function getHeading(): string
    {
        return 'Generate Purchase Order';
    }

    public function getSubheading(): ?string
    {
        return 'Approval and Purchase Order generation workbench.';
    }

    public function getBreadcrumb(): string
    {
        return 'Generate PO';
    }

    /*
    |--------------------------------------------------------------------------
    | Disable Save
    |--------------------------------------------------------------------------
    */

    protected function getFormActions(): array
    {
        return [];
    }
}