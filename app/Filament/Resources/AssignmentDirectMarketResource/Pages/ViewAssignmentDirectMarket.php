<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentDirectMarketResource\Pages;

use App\Filament\Resources\AssignmentDirectMarketResource\AssignmentDirectMarketResource;
use App\Livewire\Purchasing\AssignmentDirectMarketItemsGrid;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

use App\Filament\Resources\AssignmentDirectMarketResource\Actions\ApproveAssignmentDirectMarket;
use App\Filament\Resources\AssignmentDirectMarketResource\Actions\RejectAssignmentDirectMarket;

class ViewAssignmentDirectMarket extends ViewRecord
{
    protected static string $resource =
        AssignmentDirectMarketResource::class;

    /*
    |--------------------------------------------------------------------------
    | PAGE BACKGROUND
    |--------------------------------------------------------------------------
    |
    | Match the View page background with the Edit Assignment Direct Market
    | page without changing any functional behavior.
    |
    */

    public function getExtraBodyAttributes(): array
    {
        return [
            'class' => 'bg-gray-50',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [

            ApproveAssignmentDirectMarket::make(),

            RejectAssignmentDirectMarket::make(),

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
                | ADM INFORMATION
                |--------------------------------------------------------------------------
                */

                $this->getInfolistContentComponent(),

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
                            true,
                    ])
                    ->columnSpanFull(),

            ]);
    }
}