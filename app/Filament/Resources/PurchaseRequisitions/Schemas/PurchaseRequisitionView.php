<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class PurchaseRequisitionView
{
    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema
            ->components([

                Section::make('General Information')
                    ->description('Material Requisition document information.')
                    ->schema(
                        PurchaseRequisitionGeneralInformation::schema()
                    )
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Material Requisition Items')
                    ->description('List of requested items.')
                    ->schema([
                        View::make(
                            'filament.resources.purchase-requisitions.pages.purchase-requisition-items-table-view'
                        ),
                    ])
                    ->columnSpanFull(),

                Section::make('Summary')
                    ->schema(
                        PurchaseRequisitionSummary::schema()
                    )
                    ->columns(4),

            ]);
    }
}