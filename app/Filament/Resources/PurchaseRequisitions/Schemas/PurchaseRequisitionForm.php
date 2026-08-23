<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Schemas;

use App\Filament\Resources\PurchaseRequisitions\Actions\PurchaseRequisitionItemActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PurchaseRequisitionForm
{
    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | General Information
                |--------------------------------------------------------------------------
                */

                Section::make('General Information')
                    ->description('Material Requisition document information.')
                    ->schema([

                        DatePicker::make('request_date')
                            ->label('Request Date')
                            ->native(false)
                            ->default(now())
                            ->live()
                            ->required(),

                        DatePicker::make('required_date')
                            ->label('Required Date')
                            ->native(false)
                            ->minDate(fn (Get $get) => $get('request_date'))
                            ->required(),


                        Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'company_name')
                            ->getOptionLabelFromRecordUsing(
                                fn (\App\Models\Company $record): string =>
                                    "{$record->company_code} - {$record->company_name}"
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                            

                        Select::make('branch_id')
                            ->label('Branch')
                            ->relationship('branch', 'branch_name')
                            ->getOptionLabelFromRecordUsing(
                                fn (\App\Models\Branch $record): string =>
                                    "{$record->branch_code} - {$record->branch_name}"
                            )
                            ->searchable()
                            ->preload()
                            ->required(),                       



                        Select::make('business_unit_id')
                            ->label('Business Unit')
                            ->relationship('businessUnit', 'business_unit_name')
                            ->getOptionLabelFromRecordUsing(
                                fn (\App\Models\BusinessUnit $record): string =>
                                    "{$record->business_unit_code} - {$record->business_unit_name}"
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'department_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('section_id')
                            ->label('Section')
                            ->relationship('section', 'section_name')
                            ->searchable()
                            ->preload(),

                        Select::make('cost_center_id')
                            ->label('Cost Center')
                            ->relationship('costCenter', 'cost_center_name')
                            ->searchable()
                            ->preload(),

                        Select::make('warehouse_id')
                            ->label('Warehouse')
                            ->relationship('warehouse', 'warehouse_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('currency_id')
                            ->label('Currency')
                            ->relationship(
                                'currency',
                                'currency_name',
                                fn ($query) => $query->active()
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (\App\Models\Currency $record): string =>
                                    $record->display_name
                            )
                            ->searchable(['currency_code', 'currency_name'])
                            ->preload()
                            ->required(),

                        Select::make('requester_id')
                            ->label('Requester')
                            ->relationship('requester', 'name')
                            ->searchable()
                            ->preload()
                            ->default(auth()->id())
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        Textarea::make('remarks')
                            ->label('Remarks')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible(false),

                /*
                |--------------------------------------------------------------------------
                | Material Requisition Items
                |--------------------------------------------------------------------------
                */

                Section::make('Material Requisition Items')
                    ->description('List of requested items.')
                    ->headerActions([
                        PurchaseRequisitionItemActions::add(),
                        PurchaseRequisitionItemActions::import(),
                        PurchaseRequisitionItemActions::copy(),
                        PurchaseRequisitionItemActions::clear(),
                    ])
                    ->schema([
                        View::make(
                            'filament.resources.purchase-requisitions.pages.purchase-requisition-items-table-edit'
                        ),
                    ])
                    ->columnSpanFull()
                    ->collapsible(false),

                /*
                |--------------------------------------------------------------------------
                | Summary
                |--------------------------------------------------------------------------
                */

                Section::make('Summary')
                    ->hidden()
                    ->description('Material Requisition summary.')
                    ->schema([

                        Placeholder::make('total_items')
                            ->label('Total Items'),

                        Placeholder::make('total_quantity')
                            ->label('Total Quantity'),

                        Placeholder::make('estimated_amount')
                            ->label('Estimated Amount'),

                        Placeholder::make('status_summary')
                            ->label('Status'),

                    ])
                    ->columns(4)
                    ->collapsible(false),

            ]);
    }
}