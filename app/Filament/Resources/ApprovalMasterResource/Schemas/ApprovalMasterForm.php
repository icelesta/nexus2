<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApprovalMasterResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApprovalMasterForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Approval Master Information
                |--------------------------------------------------------------------------
                */

                Section::make('Approval Master Information')
                    ->description(
                        'Define the approval workflow configuration for a Nexus module.'
                    )
                    ->schema([

                        TextInput::make('code')
                            ->label('Code')
                            ->required()
                            ->maxLength(50)
                            ->unique(
                                table: 'approval_masters',
                                column: 'code',
                                ignoreRecord: true,
                            )
                            ->placeholder('e.g. MR-APPROVAL')
                            ->helperText(
                                'Unique identifier for this approval master.'
                            ),

                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(150)
                            ->placeholder(
                                'e.g. Material Requisition Approval'
                            ),

                        Select::make('module')
                            ->label('Module')
                            ->required()
                            ->options([
                                'MATERIAL_REQUISITION' => 'Material Requisition',
                                'PURCHASE_ORDER'       => 'Purchase Order',
                                'GOODS_RECEIPT'        => 'Goods Receipt',
                            ])
                            ->searchable()
                            ->native(false)
                            ->placeholder('Select module')
                            ->helperText(
                                'Select the Nexus module that will use this approval master.'
                            )
                            ->unique(
                                table: 'approval_masters',
                                column: 'module',
                                ignoreRecord: true,
                            ),

                        Select::make('is_active')
                            ->label('Status')
                            ->options([
                                true  => 'Active',
                                false => 'Inactive',
                            ])
                            ->default(true)
                            ->required()
                            ->native(false),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull()
                            ->placeholder(
                                'Describe the purpose of this approval workflow.'
                            ),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }
}