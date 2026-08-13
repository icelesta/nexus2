<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Actions;

use App\Models\Item;
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

use Filament\Notifications\Notification;
use Livewire\Component;
use Filament\Forms\Get;



class PurchaseRequisitionItemActions
{

    protected static function isViewPage(): bool
    {
        return request()->routeIs(
            'filament.admin.resources.purchase-requisitions.view'
        );
    }

    /**
     * Add Material Requisition Item.
     */
    public static function add(): Action
    {
        return Action::make('addItem')
            ->label('Add Item')
            ->icon('heroicon-m-plus')
            ->color('warning')

            ->modalHeading('Add Material Item')
            ->modalDescription('Select an item from Item Master.')
            ->modalWidth('4xl')

            ->form([

                Select::make('item_id')
                    ->label('Item')
                    ->options(
                        Item::query()
                            ->orderBy('item_code')
                            ->get()
                            ->mapWithKeys(fn (Item $item) => [
                                $item->id => "{$item->item_code} - {$item->item_name}",
                            ])
                    )
                    ->searchable()
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set): void {

                        if (! $state) {
                            return;
                        }

                        $item = Item::find($state);

                        if (! $item) {
                            return;
                        }

                        $defaults = app(PurchaseRequisitionItemService::class)
                            ->buildDefaults($item);

                        foreach ($defaults as $field => $value) {
                            $set($field, $value);
                        }
                    }),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3),

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->default(1)
                    ->required(),

                Select::make('uom_id')
                    ->label('Unit of Measure')
                    ->options(
                        Uom::query()
                            ->orderBy('uom_name')
                            ->pluck('uom_name', 'id')
                    )
                    ->disabled()
                    ->dehydrated(),

                DatePicker::make('required_date')
                    ->label('Required Date')
                    ->native(false)
                    ->default(now())
                    ->required(),

                Select::make('warehouse_id')
                    ->label('Warehouse')
                    ->options(
                        Warehouse::query()
                            ->orderBy('warehouse_name')
                            ->pluck('warehouse_name', 'id')
                    )
                    ->searchable(),

            ])

            ->action(function (array $data, Schema $schema): void {

                $record = $schema->getRecord();

                if (! $record) {

                    Notification::make()
                        ->title('Save Material Requisition First')
                        ->body('Please save the Material Requisition header before adding items.')
                        ->warning()
                        ->persistent()
                        ->send();

                    return;
                }

                app(PurchaseRequisitionItemService::class)
                    ->create(
                        purchaseRequisition: $record,
                        data: $data,
                    );

            });
    }


    /**
     * Edit Material Requisition Item.
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
                            ->mapWithKeys(fn (Item $item) => [
                                $item->id => "{$item->item_code} - {$item->item_name}",
                            ])
                    )
                    ->searchable()
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set): void {

                        if (! $state) {
                            return;
                        }

                        $item = Item::find($state);

                        if (! $item) {
                            return;
                        }

                        $defaults = app(PurchaseRequisitionItemService::class)
                            ->buildDefaults($item);

                        foreach ($defaults as $field => $value) {
                            $set($field, $value);
                        }
                    }),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3),

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required(),

                Select::make('uom_id')
                    ->label('Unit of Measure')
                    ->options(
                        Uom::query()
                            ->orderBy('uom_name')
                            ->pluck('uom_name', 'id')
                    )
                    ->searchable(),

                DatePicker::make('required_date')
                    ->label('Required Date')
                    ->native(false)
                    ->required(),

                Select::make('warehouse_id')
                    ->label('Warehouse')
                    ->options(
                        Warehouse::query()
                            ->orderBy('warehouse_name')
                            ->pluck('warehouse_name', 'id')
                    )
                    ->searchable(),

            ]);

        // Sprint 4.2.5
        // fillForm() + Service::update()
    }

    /**
     * Delete Material Requisition Item.
     *
     * Sprint 4.2.5
     */
    public static function delete(): Action
    {
        return Action::make('deleteItem')
            ->label('Delete Item')
            ->icon('heroicon-m-trash')
            ->color('danger');
    }

    public static function import(): Action
    {
        return Action::make('importItem')
            ->label('Import Excel')
            ->icon('heroicon-m-arrow-up-tray')
            ->color('gray')
            ->disabled();
    }

    public static function copy(): Action
    {
        return Action::make('copyItem')
            ->label('Copy From PR')
            ->icon('heroicon-m-document-duplicate')
            ->color('gray')
            ->disabled();
    }


    public static function clear(): Action
    {
        return Action::make('clearItem')
            ->label('Clear')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Clear Material Requisition Items')
            ->modalDescription('Delete all items from this Material Requisition?')
            ->action(function (Component $livewire): void {

                $livewire->clearItems();

            });
    }

}