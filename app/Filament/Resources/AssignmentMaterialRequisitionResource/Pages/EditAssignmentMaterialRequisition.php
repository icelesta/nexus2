<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Pages;

use App\Filament\Resources\AssignmentMaterialRequisitionResource\AssignmentMaterialRequisitionResource;
use App\Livewire\Purchasing\AssignmentItemsGrid;
use App\Models\AssignmentMaterialRequisition;
use App\Services\Purchasing\AssignmentMaterialRequisitionService;

use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

use Filament\Resources\Pages\EditRecord;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;


class EditAssignmentMaterialRequisition extends EditRecord
{
    protected static string $resource = AssignmentMaterialRequisitionResource::class;


    protected function getHeaderActions(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Submit AMR
            |--------------------------------------------------------------------------
            |
            | AMR workflow ends here.
            |
            | Updated
            |     ↓
            | Submit
            |     ↓
            | Waiting Approval
            |
            | Approval is NOT performed on AMR.
            | Further approval is handled from the
            | Generate Purchase Order workbench.
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
                    fn () =>
                        $this->submitAssignment()
                ),

        ];
    }

    protected function getFormActions(): array
    {
        return [

            Action::make('save')
                ->label('Save Changes')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save')
                ->visible(fn (): bool => $this->record->canEdit()),

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
                ->visible(fn (): bool => $this->record->canEdit()),


        ];
    }

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
        app(AssignmentMaterialRequisitionService::class)
            ->submit($this->record->id);

        $this->refreshRecord();

        $this->success(
            'Assignment submitted successfully.'
        );

        $this->redirect(
            static::getResource()::getUrl('index')
        );
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->id();

        $record->update($data);

        return $record;
    }

}