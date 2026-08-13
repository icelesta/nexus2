<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Actions;

use App\Models\Item;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\Uom;
use App\Models\Warehouse;
use App\Services\Purchasing\PurchaseRequisitionItemService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Livewire\Component;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequisitionItemActions
{
    protected static function isViewPage(): bool
    {
        return request()->routeIs(
            'filament.admin.resources.purchase-requisitions.view'
        );
    }

    protected static function isCreatePage(): bool
    {
        return request()->routeIs(
            'filament.admin.resources.purchase-requisitions.create'
        );
    }

    /**
     * -------------------------------------------------------------------------
     * ADD MATERIAL REQUISITION ITEM
     * -------------------------------------------------------------------------
     *
     * Editable:
     * - Item
     * - Description
     * - Quantity
     *
     * Automatically inherited:
     * - Unit of Measure     -> Item Master
     * - Required Date       -> MR Header
     * - Warehouse           -> MR Header
     *
     * Required Date and Warehouse are READONLY at item level.
     */
    public static function add(): Action
    {
        return Action::make('addItem')
            ->label('Add Item')
            ->icon('heroicon-m-plus')
            ->color('warning')

            ->disabled(fn () =>
                self::isViewPage()
                || self::isCreatePage()
            )

            ->modalHeading('Add Material Item')
            ->modalDescription('Select an item from Item Master.')
            ->modalWidth('4xl')

            /*
            |--------------------------------------------------------------------------
            | LOAD HEADER VALUES WHEN MODAL OPENS
            |--------------------------------------------------------------------------
            |
            | Required Date and Warehouse belong to the MR Header.
            |
            */

            ->fillForm(function (?Model $record): array {

                if (! $record instanceof PurchaseRequisition) {
                    return [
                        'required_date' => null,
                        'warehouse_id'  => null,
                    ];
                }

                return [
                    'required_date' => $record->required_date,
                    'warehouse_id'  => $record->warehouse_id,
                ];
            })

            ->form([

                /*
                |--------------------------------------------------------------------------
                | ITEM
                |--------------------------------------------------------------------------
                |
                | USER CAN EDIT
                |
                */

                Select::make('item_id')
                    ->label('Item')
                    ->options(
                        Item::query()
                            ->orderBy('item_code')
                            ->get()
                            ->mapWithKeys(
                                fn (Item $item): array => [
                                    $item->id =>
                                        "{$item->item_code} - {$item->item_name}",
                                ]
                            )
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()

                    ->afterStateUpdated(
                        function (
                            $state,
                            callable $set
                        ): void {

                            if (! $state) {
                                $set('description', null);
                                $set('uom_id', null);

                                return;
                            }

                            $item = Item::query()->find($state);

                            if (! $item) {
                                return;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Description
                            |--------------------------------------------------------------------------
                            |
                            | Auto-filled from Item Master but remains editable.
                            |
                            */

                            $set(
                                'description',
                                $item->description
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | Unit of Measure
                            |--------------------------------------------------------------------------
                            |
                            | UOM comes from Item Master.
                            |
                            */

                            $set(
                                'uom_id',
                                $item->base_uom_id
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | IMPORTANT
                            |--------------------------------------------------------------------------
                            |
                            | DO NOT set warehouse_id here.
                            |
                            | Warehouse belongs to MR Header.
                            |
                            */
                        }
                    ),

                /*
                |--------------------------------------------------------------------------
                | DESCRIPTION
                |--------------------------------------------------------------------------
                */

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3),

                /*
                |--------------------------------------------------------------------------
                | QUANTITY
                |--------------------------------------------------------------------------
                */

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->default(1)
                    ->minValue(0.000001)
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | UNIT OF MEASURE
                |--------------------------------------------------------------------------
                |
                | Item Master controlled.
                | Readonly but dehydrated so value is submitted.
                |
                */

                Select::make('uom_id')
                    ->label('Unit of Measure')
                    ->options(
                        Uom::query()
                            ->orderBy('uom_name')
                            ->pluck('uom_name', 'id')
                    )
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | REQUIRED DATE
                |--------------------------------------------------------------------------
                |
                | MR HEADER CONTROLLED.
                |
                | User CANNOT change this value here.
                |
                */

                DatePicker::make('required_date')
                    ->label('Required Date')
                    ->native(false)
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | WAREHOUSE
                |--------------------------------------------------------------------------
                |
                | MR HEADER CONTROLLED.
                |
                | User CANNOT change this value here.
                |
                */

                Select::make('warehouse_id')
                    ->label('Warehouse')
                    ->options(
                        Warehouse::query()
                            ->orderBy('warehouse_name')
                            ->pluck('warehouse_name', 'id')
                    )
                    ->disabled()
                    ->dehydrated()
                    ->required(),

            ])

            /*
            |--------------------------------------------------------------------------
            | SAVE ITEM
            |--------------------------------------------------------------------------
            */

            ->action(
                function (
                    array $data,
                    Schema $schema
                ): void {

                    $record = $schema->getRecord();

                    if (! $record instanceof PurchaseRequisition) {
                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | HARD HEADER ENFORCEMENT
                    |--------------------------------------------------------------------------
                    |
                    | Do not trust modal state for these fields.
                    |
                    | Always take the latest values from MR Header.
                    |
                    */

                    $data['required_date'] = $record->required_date;
                    $data['warehouse_id']  = $record->warehouse_id;

                    /*
                    |--------------------------------------------------------------------------
                    | HARD UOM ENFORCEMENT
                    |--------------------------------------------------------------------------
                    |
                    | Always take UOM from selected Item Master.
                    |
                    */

                    if (! empty($data['item_id'])) {

                        $item = Item::query()
                            ->find($data['item_id']);

                        if ($item) {
                            $data['uom_id'] = $item->base_uom_id;
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE THROUGH EXISTING SERVICE
                    |--------------------------------------------------------------------------
                    */

                    app(PurchaseRequisitionItemService::class)
                        ->create(
                            purchaseRequisition: $record,
                            data: $data,
                        );
                }
            );
    }

    /**
     * -------------------------------------------------------------------------
     * EDIT MATERIAL REQUISITION ITEM
     * -------------------------------------------------------------------------
     */
    public static function edit(): Action
    {
        return Action::make('editItem')
            ->label('Edit Item')
            ->icon('heroicon-m-pencil-square')
            ->color('gray')

            ->modalHeading('Edit Material Item')
            ->modalDescription('Update selected material item.')
            ->modalWidth('4xl')

            ->form([

                Select::make('item_id')
                    ->label('Item')
                    ->options(
                        Item::query()
                            ->orderBy('item_code')
                            ->get()
                            ->mapWithKeys(
                                fn (Item $item): array => [
                                    $item->id =>
                                        "{$item->item_code} - {$item->item_name}",
                                ]
                            )
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()

                    ->afterStateUpdated(
                        function (
                            $state,
                            callable $set
                        ): void {

                            if (! $state) {
                                return;
                            }

                            $item = Item::query()->find($state);

                            if (! $item) {
                                return;
                            }

                            $set(
                                'description',
                                $item->description
                            );

                            $set(
                                'uom_id',
                                $item->base_uom_id
                            );
                        }
                    ),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3),

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | UOM
                |--------------------------------------------------------------------------
                */

                Select::make('uom_id')
                    ->label('Unit of Measure')
                    ->options(
                        Uom::query()
                            ->orderBy('uom_name')
                            ->pluck('uom_name', 'id')
                    )
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | REQUIRED DATE
                |--------------------------------------------------------------------------
                |
                | Always follows MR Header.
                |
                */

                DatePicker::make('required_date')
                    ->label('Required Date')
                    ->native(false)
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                /*
                |--------------------------------------------------------------------------
                | WAREHOUSE
                |--------------------------------------------------------------------------
                |
                | Always follows MR Header.
                |
                */

                Select::make('warehouse_id')
                    ->label('Warehouse')
                    ->options(
                        Warehouse::query()
                            ->orderBy('warehouse_name')
                            ->pluck('warehouse_name', 'id')
                    )
                    ->disabled()
                    ->dehydrated()
                    ->required(),

            ]);
    }

    /**
     * -------------------------------------------------------------------------
     * DELETE ITEM
     * -------------------------------------------------------------------------
     */
    public static function delete(): Action
    {
        return Action::make('deleteItem')
            ->label('Delete Item')
            ->icon('heroicon-m-trash')
            ->color('danger');
    }

    /**
     * -------------------------------------------------------------------------
     * IMPORT
     * -------------------------------------------------------------------------
     */
    public static function import(): Action
    {
        return Action::make('importItem')
            ->label('Import Excel')
            ->icon('heroicon-m-arrow-up-tray')
            ->color('gray')
            ->disabled(fn () => self::isViewPage());
    }

    /**
     * -------------------------------------------------------------------------
     * COPY FROM PR
     * -------------------------------------------------------------------------
     */
    public static function copy(): Action
    {
        return Action::make('copyItem')
            ->label('Copy From PR')
            ->icon('heroicon-m-document-duplicate')
            ->color('gray')
            ->disabled(fn () => self::isViewPage());
    }

    /**
     * -------------------------------------------------------------------------
     * CLEAR ITEMS
     * -------------------------------------------------------------------------
     */
    public static function clear(): Action
    {
        return Action::make('clearItem')
            ->label('Clear')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->disabled(fn () => self::isViewPage())
            ->requiresConfirmation()
            ->modalHeading('Clear Material Requisition Items')
            ->modalDescription(
                'Delete all items from this Material Requisition?'
            )
            ->action(function (Component $livewire): void {
                $livewire->clearItems();
            });
    }
}