<?php

declare(strict_types=1);

namespace App\Filament\Resources\GeneratePurchaseOrderResource;

use App\Filament\Resources\GeneratePurchaseOrderResource\Pages\EditGeneratePurchaseOrder;
use App\Filament\Resources\GeneratePurchaseOrderResource\Pages\ListGeneratePurchaseOrders;
use App\Filament\Resources\GeneratePurchaseOrderResource\Schemas\GeneratePurchaseOrderForm;
use App\Filament\Resources\GeneratePurchaseOrderResource\Tables\GeneratePurchaseOrdersTable;
use App\Models\AssignmentMaterialRequisition;
use Illuminate\Database\Eloquent\Model;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class GeneratePurchaseOrderResource extends Resource
{
    /*
    |--------------------------------------------------------------------------
    | Resource
    |--------------------------------------------------------------------------
    */

    protected static ?string $model =
        AssignmentMaterialRequisition::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static UnitEnum|string|null $navigationGroup =
        'Purchasing';

    protected static ?int $navigationSort = 45;

    protected static ?string $navigationLabel =
        'Generate Purchase Order';

    protected static ?string $modelLabel =
        'Generate Purchase Order';

    protected static ?string $pluralModelLabel =
        'Generate Purchase Orders';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedDocumentPlus;

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(
        Schema $schema
    ): Schema {

        return GeneratePurchaseOrderForm::configure(
            $schema
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(
        Table $table
    ): Table {

        return GeneratePurchaseOrdersTable::configure(
            $table
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public static function getRelations(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [

            'index' =>
                ListGeneratePurchaseOrders::route('/'),

            'edit' =>
                EditGeneratePurchaseOrder::route(
                    '/{record}/edit'
                ),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    |
    | Workbench only shows AMR transactions that have
    | reached the approval stage.
    |
    */

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'purchaseRequisition',
                'items.purchaseRequisitionItem.item',
                'items.supplier',
                'purchaseOrder',
            ])
            ->where(
                'status',
                AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL
            )
            ->whereDoesntHave(
                'purchaseOrder'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->hasPermissionTo(
            'Update:AssignmentMaterialRequisition'
        ) === true
            && $record->status === AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL;
    }

}