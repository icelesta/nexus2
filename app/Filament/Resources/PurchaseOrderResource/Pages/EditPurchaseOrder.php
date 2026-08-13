<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource\PurchaseOrderResource;
use App\Livewire\Purchasing\PurchaseOrderItemsGrid;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;

use Filament\Notifications\Notification;

use Filament\Resources\Pages\EditRecord;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\View;


use Illuminate\Database\Eloquent\Model;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [

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

            DeleteAction::make()
                ->visible(
                    fn (): bool => $this->record->canDelete()
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
        return [

            Action::make('save')
                ->label('Save Changes')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save')
                ->visible(
                    fn (): bool => $this->record->canEdit()
                ),

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

        ];
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

                View::make(
                    'filament.resources.purchase-orders.pages.partials.document-header'
                )
                    ->viewData([
                        'record' => $this->record,
                    ])
                    ->columnSpanFull(),

                Group::make([

                    EmbeddedSchema::make('form'),

                ]),

                Group::make([

                    $this->getFormActionsContentComponent(),

                ])
                    ->columnSpanFull(),

                Livewire::make(PurchaseOrderItemsGrid::class)
                    ->key('purchase-order-items-grid')
                    ->data([
                        'purchaseOrder' => $this->record,
                        'readonly'      => false,
                    ])
                    ->columnSpanFull(),

            ]);
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

    protected function success(string $message): void
    {
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

        $data['updated_by'] = auth()->id();

        $record->update($data);

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

            'number' => $this->record->document_no,

            'date' => $this->record->document_date,

            'status' => $this->record->status,

        ];
    }
}