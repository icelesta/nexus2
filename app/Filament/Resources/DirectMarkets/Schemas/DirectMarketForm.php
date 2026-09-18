<?php

declare(strict_types=1);

namespace App\Filament\Resources\DirectMarkets\Schemas;

use App\Models\DirectMarket;
use App\Models\Item;
use App\Models\Uom;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DirectMarketForm
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema

            ->columns(1)

            ->components([

                /*
                |--------------------------------------------------------------------------
                | DIRECT MARKET INFORMATION
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Direct Market Information'
                )
                    ->description(
                        'Create a Direct Market request for non-stock / non-warehouse purchasing.'
                    )
                    ->disabled(fn ($livewire): bool =>
                        method_exists($livewire, 'isLevelOneApprovalEdit')
                        && $livewire->isLevelOneApprovalEdit()
                    )
                    ->columnSpanFull()
                    ->schema([

                        Grid::make(12)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | REQUEST DATE
                                |--------------------------------------------------------------------------
                                */

                                DatePicker::make(
                                    'request_date'
                                )
                                    ->label(
                                        'Request Date'
                                    )
                                    ->native(false)
                                    ->default(
                                        today()
                                    )
                                    ->minDate(
                                        fn (string $operation) =>
                                            $operation === 'create'
                                                ? today()
                                                : null
                                    )
                                    ->disabled()
                                    ->dehydrated()
                                    ->live()
                                    ->required()
                                    ->columnSpan(3),

                                /*
                                |--------------------------------------------------------------------------
                                | REQUIRED DATE
                                |--------------------------------------------------------------------------
                                */

                                DatePicker::make(
                                    'required_date'
                                )
                                    ->label(
                                        'Required Date'
                                    )
                                    ->native(false)
                                    ->minDate(
                                        fn ($get) =>
                                            $get(
                                                'request_date'
                                            )
                                    )
                                    ->live()
                                    ->afterStateUpdated(
                                        function (
                                            $state,
                                            $set,
                                            $get
                                        ): void {

                                            $items =
                                                $get('items') ?? [];

                                            foreach (
                                                $items as $key => $item
                                            ) {
                                                $items[$key]['required_date'] =
                                                    $state;
                                            }

                                            $set(
                                                'items',
                                                $items
                                            );
                                        }
                                    )
                                    ->required()
                                    ->columnSpan(3),

                                /*
                                |--------------------------------------------------------------------------
                                | COMPANY
                                |--------------------------------------------------------------------------
                                */

                                Select::make(
                                    'company_id'
                                )
                                    ->label(
                                        'Company'
                                    )
                                    ->relationship(
                                        'company',
                                        'company_name'
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(3),

                                /*
                                |--------------------------------------------------------------------------
                                | BUSINESS UNIT
                                |--------------------------------------------------------------------------
                                */

                                Select::make(
                                    'business_unit_id'
                                )
                                    ->label(
                                        'Business Unit'
                                    )
                                    ->relationship(
                                        'businessUnit',
                                        'business_unit_name'
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(3),

                                /*
                                |--------------------------------------------------------------------------
                                | BRANCH
                                |--------------------------------------------------------------------------
                                */

                                Select::make(
                                    'branch_id'
                                )
                                    ->label(
                                        'Branch'
                                    )
                                    ->relationship(
                                        'branch',
                                        'branch_name'
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(3),

                                /*
                                |--------------------------------------------------------------------------
                                | DEPARTMENT
                                |--------------------------------------------------------------------------
                                */

                                Select::make(
                                    'department_id'
                                )
                                    ->label(
                                        'Department'
                                    )
                                    ->relationship(
                                        'department',
                                        'department_name'
                                    )
                                    ->default(
                                        fn (): ?int =>
                                            auth()->user()?->department_id
                                    )
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(3),

                                /*
                                |--------------------------------------------------------------------------
                                | COST CENTER
                                |--------------------------------------------------------------------------
                                */

                                Select::make(
                                    'cost_center_id'
                                )
                                    ->label(
                                        'Cost Center'
                                    )
                                    ->relationship(
                                        'costCenter',
                                        'cost_center_name'
                                    )
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->columnSpan(3),

                                /*
                                |--------------------------------------------------------------------------
                                | REQUESTER
                                |--------------------------------------------------------------------------
                                */

                                Select::make(
                                    'requester_id'
                                )
                                    ->label(
                                        'Requester'
                                    )
                                    ->relationship(
                                        'requester',
                                        'name'
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->default(
                                        fn (): ?int =>
                                            auth()->id()
                                    )
                                    ->required()
                                    ->columnSpan(3),

                                Select::make(
                                    'warehouse_id'
                                )
                                    ->label(
                                        'Warehouse'
                                    )
                                    ->relationship(
                                        'warehouse',
                                        'warehouse_name'
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(6),

                                Select::make(
                                    'currency_id'
                                )
                                    ->label(
                                        'Currency'
                                    )
                                    ->relationship(
                                        'currency',
                                        'currency_name',
                                        fn ($query) => $query->active()
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn (\App\Models\Currency $record): string =>
                                            $record->display_name
                                    )
                                    ->searchable([
                                        'currency_code',
                                        'currency_name',
                                    ])
                                    ->preload()
                                    ->required()
                                    ->columnSpan(6),

                                /*
                                |--------------------------------------------------------------------------
                                | REMARKS
                                |--------------------------------------------------------------------------
                                */

                                Textarea::make(
                                    'remarks'
                                )
                                    ->label(
                                        'Remarks'
                                    )
                                    ->rows(3)
                                    ->maxLength(5000)
                                    ->columnSpanFull(),

                            ]),

                    ]),
            /*
            |--------------------------------------------------------------------------
            | DIRECT MARKET ITEMS
            |--------------------------------------------------------------------------
            */

            Section::make(
                'Direct Market Items'
            )
                ->description(
                    'Add the non-stock / non-warehouse items required for this Direct Market.'
                )
                ->columnSpanFull()
                ->schema([

                    Repeater::make(
                        'items'
                    )
                        ->label(
                            'Items'
                        )
                        ->schema([

                            /*
                            |--------------------------------------------------------------------------
                            | EXISTING ITEM ID
                            |--------------------------------------------------------------------------
                            */

                            Hidden::make(
                                'id'
                            ),

                            /*
                            |--------------------------------------------------------------------------
                            | ITEM
                            |--------------------------------------------------------------------------
                            */

                            Select::make(
                                'item_id'
                            )
                                ->label(
                                    'Item'
                                )
                                ->options(
                                    fn (): array =>
                                        Item::query()
                                            ->orderBy(
                                                'item_name'
                                            )
                                            ->pluck(
                                                'item_name',
                                                'id'
                                            )
                                            ->all()
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(
                                    function (
                                        $state,
                                        $set
                                    ): void {

                                        if (
                                            blank($state)
                                        ) {
                                            $set(
                                                'uom_id',
                                                null
                                            );

                                            return;
                                        }

                                        $item =
                                            Item::query()
                                                ->find($state);

                                        $set(
                                            'uom_id',
                                            $item?->base_uom_id
                                        );
                                    }
                                )
                                ->disabled(fn ($livewire): bool =>
                                    method_exists($livewire, 'isLevelOneApprovalEdit')
                                    && $livewire->isLevelOneApprovalEdit()
                                )

                                ->columnSpan(4),

                            /*
                            |--------------------------------------------------------------------------
                            | UOM
                            |--------------------------------------------------------------------------
                            */

                            Select::make(
                                'uom_id'
                            )
                                ->label(
                                    'UOM'
                                )
                                ->options(
                                    function (
                                        $get
                                    ): array {

                                        $itemId =
                                            $get('item_id');

                                        if (
                                            blank($itemId)
                                        ) {
                                            return [];
                                        }

                                        $item =
                                            Item::query()
                                                ->find($itemId);

                                        $uomId =
                                            $item?->base_uom_id;

                                        if (
                                            blank($uomId)
                                        ) {
                                            return [];
                                        }

                                        return Uom::query()
                                            ->where(
                                                'id',
                                                $uomId
                                            )
                                            ->pluck(
                                                'uom_name',
                                                'id'
                                            )
                                            ->all();
                                    }
                                )
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->columnSpan(2),

                            /*
                            |--------------------------------------------------------------------------
                            | QTY
                            |--------------------------------------------------------------------------
                            */

                            TextInput::make(
                                'qty'
                            )
                                ->label(
                                    'Qty'
                                )
                                ->numeric()
                                ->minValue(
                                    0.0001
                                )
                                ->step(
                                    0.01
                                )
                                ->required()
                                ->columnSpan(2),

                            /*
                            |--------------------------------------------------------------------------
                            | REQUIRED DATE
                            |--------------------------------------------------------------------------
                            */

                            DatePicker::make(
                                'required_date'
                            )
                                ->label(
                                    'Required Date'
                                )
                                ->native(false)
                                ->default(
                                    fn ($get) =>
                                        $get(
                                            '../../required_date'
                                        )
                                )
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->disabled(fn ($livewire): bool =>
                                    method_exists($livewire, 'isLevelOneApprovalEdit')
                                    && $livewire->isLevelOneApprovalEdit()
                                )

                                ->columnSpan(3),

                            /*
                            |--------------------------------------------------------------------------
                            | ITEM REMARKS
                            |--------------------------------------------------------------------------
                            */

                            Textarea::make(
                                'remarks'
                            )
                                ->label(
                                    'Item Remarks'
                                )
                                ->rows(1)
                                ->autosize()
                                ->maxLength(5000)
                                ->disabled(fn ($livewire): bool =>
                                    method_exists($livewire, 'isLevelOneApprovalEdit')
                                    && $livewire->isLevelOneApprovalEdit()
                                )
                                ->columnSpan(5),

                        ])
                        ->columns(16)
                        ->defaultItems(1)
                        ->minItems(1)
                        ->addActionLabel('Add Direct Market Item')
                        ->addable(fn ($livewire): bool =>
                            ! (
                                method_exists($livewire, 'isLevelOneApprovalEdit')
                                && $livewire->isLevelOneApprovalEdit()
                            )
                        )
                        ->deletable(fn ($livewire): bool =>
                            ! (
                                method_exists($livewire, 'isLevelOneApprovalEdit')
                                && $livewire->isLevelOneApprovalEdit()
                            )
                        )
                        ->reorderable(false)
                        ->collapsible()

                    ]),

            ]);
    }
}