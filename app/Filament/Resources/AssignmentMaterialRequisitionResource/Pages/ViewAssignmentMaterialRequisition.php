<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Pages;

use App\Filament\Resources\AssignmentMaterialRequisitionResource\AssignmentMaterialRequisitionResource;
use App\Livewire\Purchasing\AssignmentItemsGrid;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

use Illuminate\Support\HtmlString;


class ViewAssignmentMaterialRequisition extends ViewRecord
{
    protected static string $resource =
        AssignmentMaterialRequisitionResource::class;


    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [

            EditAction::make()
                ->authorize(
                    fn (): bool =>
                        auth()->user()->can(
                            'update',
                            $this->record
                        )
                ),

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Page Content
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
                | ERP DOCUMENT HEADER
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
                | ASSIGNMENT + MATERIAL REQUISITION INFORMATION
                |--------------------------------------------------------------------------
                |
                | Existing Golden Standard Infolist.
                |
                */

                $this->getInfolistContentComponent(),



                /*
                |--------------------------------------------------------------------------
                | ASSIGNMENT ITEMS
                |--------------------------------------------------------------------------
                |
                | Read-only on View page.
                |
                */

                Livewire::make(
                    AssignmentItemsGrid::class
                )
                    ->key(
                        'assignment-items-grid'
                    )
                    ->data([
                        'assignment' =>
                            $this->getRecord(),

                        'readonly' =>
                            true,
                    ])
                    ->columnSpanFull(),

            ]);
    }
}