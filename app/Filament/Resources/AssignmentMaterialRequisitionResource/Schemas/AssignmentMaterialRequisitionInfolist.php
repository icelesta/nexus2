<?php

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;



class AssignmentMaterialRequisitionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Assignment Information
                |--------------------------------------------------------------------------
                */

                Section::make('Assignment Information')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextEntry::make('document_no')
                                    ->label('Assignment Number'),

                                TextEntry::make('document_date')
                                    ->label('Assignment Date')
                                    ->date(),

                                TextEntry::make('assignedTo.name')
                                    ->label('Assigned To'),

                                TextEntry::make('assignedBy.name')
                                    ->label('Assigned By'),

                                TextEntry::make('status')
                                    ->badge(),

                                TextEntry::make('remarks')
                                    ->columnSpanFull(),

                            ]),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Material Requisition Information
                |--------------------------------------------------------------------------
                */

                Section::make('Material Requisition Information')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextEntry::make('pr_number')
                                    ->label('MR Number'),

                                TextEntry::make('request_date')
                                    ->label('MR Date')
                                    ->date(),

                                TextEntry::make('requester.name')
                                    ->label('Requester'),

                                TextEntry::make('department.department_name')
                                    ->label('Department'),

                                TextEntry::make('warehouse.warehouse_name')
                                    ->label('Warehouse'),

                                TextEntry::make('required_date')
                                    ->label('Required Date')
                                    ->date(),

                                TextEntry::make('priority')
                                    ->label('Priority'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge(),

                            ]),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | Audit Information
                |--------------------------------------------------------------------------
                */

                Section::make('Audit Information')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextEntry::make('createdBy.name')
                                    ->label('Created By'),

                                TextEntry::make('created_at')
                                    ->since(),

                                TextEntry::make('updatedBy.name')
                                    ->label('Updated By'),

                                TextEntry::make('updated_at')
                                    ->since(),

                            ]),

                    ]),

            ]);
    }
}