<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Pages;

use App\Filament\Resources\AssignmentMaterialRequisitionResource\AssignmentMaterialRequisitionResource;
use App\Models\AssignmentMaterialRequisition;
use App\Services\Purchasing\AssignmentMaterialRequisitionService;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

use App\Livewire\Purchasing\AssignmentItemsGrid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\View;

use Filament\Schemas\Components\Livewire;


class ViewAssignmentMaterialRequisition extends ViewRecord
{
    protected static string $resource = AssignmentMaterialRequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [

            EditAction::make()
                ->authorize(
                    fn (): bool => auth()->user()->can('update', $this->record)
                ),

            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(
                    fn (): bool => $this->record->status === AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL
                )
                ->authorize(
                    fn (): bool => auth()->user()->can('approve', $this->record)
                )
                ->action(function (
                    AssignmentMaterialRequisitionService $service,
                ): void {

                    $service->approve($this->record->id);

                    $this->record->refresh();

                    Notification::make()
                        ->success()
                        ->title('Document approved successfully.')
                        ->send();

                    $this->redirect(
                        static::getResource()::getUrl('view', [
                            'record' => $this->record,
                        ])
                    );
                }),

            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(
                    fn (): bool => $this->record->status === AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL
                )
                ->authorize(
                    fn (): bool => auth()->user()->can('reject', $this->record)
                )
                ->action(function (
                    AssignmentMaterialRequisitionService $service,
                ): void {

                    $service->reject($this->record->id);

                    $this->record->refresh();

                    Notification::make()
                        ->success()
                        ->title('Document rejected successfully.')
                        ->send();

                    $this->redirect(
                        static::getResource()::getUrl('view', [
                            'record' => $this->record,
                        ])
                    );
                }),

            Action::make('complete')
                ->label('Complete')
                ->icon('heroicon-o-check-badge')
                ->color('primary')
                ->requiresConfirmation()
                ->visible(
                    fn (): bool => $this->record->status === AssignmentMaterialRequisition::STATUS_APPROVED
                )
                ->authorize(
                    fn (): bool => auth()->user()->can('complete', $this->record)
                )
                ->action(function (
                    AssignmentMaterialRequisitionService $service,
                ): void {

                    $service->complete($this->record->id);

                    $this->record->refresh();

                    Notification::make()
                        ->success()
                        ->title('Document completed successfully.')
                        ->send();

                    $this->redirect(
                        static::getResource()::getUrl('view', [
                            'record' => $this->record,
                        ])
                    );
                }),

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
                | Assignment Information
                |--------------------------------------------------------------------------
                */

                $this->getInfolistContentComponent(),

                /*
                |--------------------------------------------------------------------------
                | Assignment Items (Read Only)
                |--------------------------------------------------------------------------
                */

                Livewire::make(
                    AssignmentItemsGrid::class
                )
                    ->key('assignment-items-grid')
                    ->data([
                        'assignment' => $this->getRecord(),
                        'readonly'   => true,
                    ])
                    ->columnSpanFull(),

            ]);
    }

}