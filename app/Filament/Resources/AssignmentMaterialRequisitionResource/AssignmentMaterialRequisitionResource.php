<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource;

use App\Filament\Resources\AssignmentMaterialRequisitionResource\Pages\CreateAssignmentMaterialRequisition;
use App\Filament\Resources\AssignmentMaterialRequisitionResource\Pages\EditAssignmentMaterialRequisition;
use App\Filament\Resources\AssignmentMaterialRequisitionResource\Pages\ListAssignmentMaterialRequisitions;
use App\Filament\Resources\AssignmentMaterialRequisitionResource\Pages\ViewAssignmentMaterialRequisition;
use App\Filament\Resources\AssignmentMaterialRequisitionResource\Schemas\AssignmentMaterialRequisitionForm;
use App\Filament\Resources\AssignmentMaterialRequisitionResource\Schemas\AssignmentMaterialRequisitionInfolist;
use App\Filament\Resources\AssignmentMaterialRequisitionResource\Tables\AssignmentMaterialRequisitionsTable;
use App\Models\AssignmentMaterialRequisition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class AssignmentMaterialRequisitionResource extends Resource
{
    /*
    |--------------------------------------------------------------------------
    | Resource
    |--------------------------------------------------------------------------
    */

    protected static ?string $model = AssignmentMaterialRequisition::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static UnitEnum|string|null $navigationGroup = 'Purchasing';

    protected static ?int $navigationSort = 40;

    protected static ?string $navigationLabel = 'Assignment MR';

    protected static ?string $navigationParentItem = 'Material Requisition';

    protected static ?string $modelLabel = 'Assignment Material Requisition';

    protected static ?string $pluralModelLabel = 'Assignment Material Requisitions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(Schema $schema): Schema
    {
        return AssignmentMaterialRequisitionForm::configure($schema);
    }

    /*
    |--------------------------------------------------------------------------
    | Infolist
    |--------------------------------------------------------------------------
    */

    public static function infolist(Schema $schema): Schema
    {
        return AssignmentMaterialRequisitionInfolist::configure($schema);
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return AssignmentMaterialRequisitionsTable::configure($table);
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
            'index'  => ListAssignmentMaterialRequisitions::route('/'),
            'create' => CreateAssignmentMaterialRequisition::route('/create'),
            'view'   => ViewAssignmentMaterialRequisition::route('/{record}'),
            'edit'   => EditAssignmentMaterialRequisition::route('/{record}/edit'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Global Search
    |--------------------------------------------------------------------------
    */

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'purchaseRequisition.pr_no',
            'status',
            'remarks',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    /*
    |--------------------------------------------------------------------------
    | Route Binding
    |--------------------------------------------------------------------------
    */

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}