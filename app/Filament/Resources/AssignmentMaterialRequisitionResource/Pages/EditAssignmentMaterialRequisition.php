<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Pages;

use App\Filament\Resources\AssignmentMaterialRequisitionResource\AssignmentMaterialRequisitionResource;
use App\Livewire\Purchasing\AssignmentItemsGrid;
use App\Models\AssignmentMaterialRequisition;
use App\Services\Purchasing\AssignmentMaterialRequisitionService;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

use Illuminate\Database\Eloquent\Model;


class EditAssignmentMaterialRequisition extends EditRecord
{
    protected static string $resource = AssignmentMaterialRequisitionResource::class;


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
            | Submit AMR
            |--------------------------------------------------------------------------
            |
            | AMR workflow:
            |
            | Updated
            |     ↓
            | Submit
            |     ↓
            | Waiting Approval
            |
            | Approval is NOT performed on AMR.
            | Further approval is handled by the next workflow stage.
            |
            |--------------------------------------------------------------------------
            */

            Action::make('submit')
                ->label('Submit')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->requiresConfirmation()
                ->visible(
                    fn (): bool =>
                        $this->record->status ===
                        AssignmentMaterialRequisition::STATUS_UPDATED
                )
                ->authorize(
                    fn (): bool =>
                        auth()->user()->can(
                            'submit',
                            $this->record
                        )
                )
                ->action(
                    function (): void {
                        $this->submitAssignment();
                    }
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
                    fn (): bool =>
                        $this->record->canEdit()
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
                    fn (): bool =>
                        $this->record->canEdit()
                ),

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Page Content
    |--------------------------------------------------------------------------
    */

    public function content(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([

                /*
                |--------------------------------------------------------------------------
                | ERP Document Header
                |--------------------------------------------------------------------------
                */

                View::make(
                    'filament.resources.assignment-material-requisitions.pages.partials.document-header'
                )
                    ->viewData([
                        'record' => $this->record,
                    ])
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Assignment Form
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
                | Assignment Items Grid
                |--------------------------------------------------------------------------
                */

                Livewire::make(
                    AssignmentItemsGrid::class
                )
                    ->key('assignment-items-grid')
                    ->data([
                        'assignment' => $this->getRecord(),
                        'readonly'   => false,
                    ])
                    ->columnSpanFull(),

            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Utility Layer
    |--------------------------------------------------------------------------
    */

    protected function refreshRecord(): void
    {
        $this->record->refresh();
    }


    protected function redirectToEdit(): void
    {
        $this->redirect(
            static::getResource()::getUrl('edit', [
                'record' => $this->record,
            ])
        );
    }


    protected function redirectToIndex(): void
    {
        $this->redirect(
            static::getResource()::getUrl('index')
        );
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
    | Workflow Layer
    |--------------------------------------------------------------------------
    */

    protected function submitAssignment(): void
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Persist Current AMR Header
            |--------------------------------------------------------------------------
            |
            | Save the current form state first.
            |
            | This is important because fields such as:
            |
            | - Shipping Address
            | - Payment Instruction
            |
            | may have been changed in the current form session.
            |
            | The authoritative business validation remains inside:
            |
            | AssignmentMaterialRequisitionService::submit()
            |
            |--------------------------------------------------------------------------
            */

            $this->save(
                shouldRedirect: false,
                shouldSendSavedNotification: false,
            );


            /*
            |--------------------------------------------------------------------------
            | Authoritative Submit Gate
            |--------------------------------------------------------------------------
            |
            | The service is the final authority for AMR submission.
            |
            | It must validate the complete document before allowing:
            |
            | Updated
            |     ↓
            | Waiting Approval
            |
            |--------------------------------------------------------------------------
            */

            app(AssignmentMaterialRequisitionService::class)
                ->submit($this->record->id);


            /*
            |--------------------------------------------------------------------------
            | Refresh Record
            |--------------------------------------------------------------------------
            */

            $this->refreshRecord();


            /*
            |--------------------------------------------------------------------------
            | Success Notification
            |--------------------------------------------------------------------------
            */

            $this->success(
                'Assignment submitted successfully.'
            );


            /*
            |--------------------------------------------------------------------------
            | Stay on AMR Edit Page
            |--------------------------------------------------------------------------
            */

            $this->redirectToEdit();

        } catch (\Throwable $exception) {

            /*
            |--------------------------------------------------------------------------
            | Log Exception
            |--------------------------------------------------------------------------
            */

            report($exception);


            /*
            |--------------------------------------------------------------------------
            | User Notification
            |--------------------------------------------------------------------------
            */

            Notification::make()
                ->danger()
                ->title(
                    'Unable to Submit Assignment Material Requisition'
                )
                ->body(
                    $exception->getMessage()
                )
                ->persistent()
                ->send();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Record Update Hook
    |--------------------------------------------------------------------------
    */

    protected function handleRecordUpdate(
        Model $record,
        array $data
    ): Model {
        $data['updated_by'] = auth()->id();

        $record->update($data);

        return $record;
    }
}