<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentDirectMarketResource\Pages;

use App\Filament\Resources\AssignmentDirectMarketResource\AssignmentDirectMarketResource;
use App\Livewire\Purchasing\AssignmentDirectMarketItemsGrid;
use App\Models\AdmLimitMaster;
use App\Models\AssignmentDirectMarket;
use App\Services\Purchasing\AssignmentDirectMarketService;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

use Illuminate\Database\Eloquent\Model;

class EditAssignmentDirectMarket extends EditRecord
{
    protected static string $resource =
        AssignmentDirectMarketResource::class;

    protected function getHeaderActions(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | CANCEL
            |--------------------------------------------------------------------------
            */

            Action::make('cancel')
                ->label('Cancel')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->url(
                    static::getResource()::getUrl('index')
                ),

            /*
            |--------------------------------------------------------------------------
            | SAVE CHANGES
            |--------------------------------------------------------------------------
            */

            Action::make('save')
                ->label('Save Changes')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save')
                ->visible(
                    fn (): bool =>
                        in_array(
                            $this->record->status,
                            [
                                AssignmentDirectMarket::STATUS_DRAFT,
                                AssignmentDirectMarket::STATUS_UPDATED,
                            ],
                            true
                        )
                ),

            /*
            |--------------------------------------------------------------------------
            | SAVE & CLOSE
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
                        static::getResource()::getUrl(
                            'index'
                        )
                    );
                })
                ->visible(
                    fn (): bool =>
                        in_array(
                            $this->record->status,
                            [
                                AssignmentDirectMarket::STATUS_DRAFT,
                                AssignmentDirectMarket::STATUS_UPDATED,
                            ],
                            true
                        )
                ),

            /*
            |--------------------------------------------------------------------------
            | SUBMIT ADM
            |--------------------------------------------------------------------------
            */

            Action::make('submit')
                ->label('Submit')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->requiresConfirmation()
                ->visible(
                    fn (): bool =>
                        $this->record->status
                        === AssignmentDirectMarket::STATUS_UPDATED
                )
                ->authorize(
                    fn (): bool =>
                        auth()->user()->can(
                            'submit',
                            $this->record
                        )
                )
                ->action(
                    fn () =>
                        $this->submitAssignment()
                ),

        ];
    }



    public function content(
        Schema $schema
    ): Schema {

        return $schema

            ->columns(1)

            ->components([

                /*
                |--------------------------------------------------------------------------
                | ERP DOCUMENT HEADER
                |--------------------------------------------------------------------------
                */

                View::make(
                    'filament.resources.assignment-direct-markets.pages.partials.document-header'
                )
                    ->viewData([
                        'record' => $this->record,
                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | ADM FORM
                |--------------------------------------------------------------------------
                */

                Group::make([

                    EmbeddedSchema::make('form'),

                ]),


                /*
                |--------------------------------------------------------------------------
                | ADM ITEMS
                |--------------------------------------------------------------------------
                */

                Livewire::make(
                    AssignmentDirectMarketItemsGrid::class
                )
                    ->key(
                        'assignment-direct-market-items-grid'
                    )
                    ->data([
                        'assignment' =>
                            $this->getRecord(),

                        'readonly' =>
                            false,
                    ])
                    ->columnSpanFull(),

            ]);
    }

    protected function refreshRecord(): void
    {
        $this->record->refresh();
    }

    protected function redirectToEdit(): void
    {
        $this->redirect(
            static::getResource()::getUrl(
                'edit',
                [
                    'record' =>
                        $this->record,
                ]
            )
        );
    }

    protected function success(
        string $message
    ): void {

        Notification::make()
            ->success()
            ->title($message)
            ->send();
    }

    protected function submitAssignment(): void
    {
        try {

            app(
                AssignmentDirectMarketService::class
            )
                ->submit(
                    $this->record->id
                );

            $this->refreshRecord();

            $this->success(
                'Assignment Direct Market submitted successfully.'
            );

            $this->redirectToEdit();

        } catch (\RuntimeException $e) {

            Notification::make()
                ->danger()
                ->title(
                    'Submit Tidak Dapat Dilanjutkan'
                )
                ->body(
                    'Assignment Items belum lengkap. '
                    . 'Pastikan Supplier dan Unit Price sudah diisi '
                    . 'sebelum melakukan Submit.'
                )
                ->persistent()
                ->send();

            return;
        }
    }

    protected function handleRecordUpdate(
        Model $record,
        array $data
    ): Model {

        $data['updated_by'] =
            auth()->id();

        $record->update($data);

        /*
        |--------------------------------------------------------------------------
        | ADM EDITED STATE
        |--------------------------------------------------------------------------
        |
        | Any editable Draft ADM remains Draft.
        |
        | Updated is reserved for workflow/service handling.
        |
        |--------------------------------------------------------------------------
        */

        return $record;
    }
}