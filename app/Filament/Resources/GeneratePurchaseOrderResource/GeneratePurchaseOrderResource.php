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
    protected static ?string $model =
        AssignmentMaterialRequisition::class;

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

    public static function form(
        Schema $schema
    ): Schema {
        return GeneratePurchaseOrderForm::configure($schema);
    }

    public static function table(
        Table $table
    ): Table {
        return GeneratePurchaseOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGeneratePurchaseOrders::route('/'),
            'edit' => EditGeneratePurchaseOrder::route('/{record}/edit'),
        ];
    }

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
            ->whereDoesntHave('purchaseOrder');
    }

    /*
    |--------------------------------------------------------------------------
    | Roles / Navigation Authorization
    |--------------------------------------------------------------------------
    |
    | Generate PO is a separate module identity. Its data source remains
    | AssignmentMaterialRequisition intentionally.
    |
    */

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('ViewAny:GeneratePurchaseOrder') === true;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('Update:GeneratePurchaseOrder') === true
            && $record->status ===
                AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL;
    }
}
