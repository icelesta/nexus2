<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApprovalMasterResource;

use App\Filament\Resources\ApprovalMasterResource\RelationManagers\ApprovalStepsRelationManager;

use App\Filament\Resources\ApprovalMasterResource\Pages\CreateApprovalMaster;
use App\Filament\Resources\ApprovalMasterResource\Pages\EditApprovalMaster;
use App\Filament\Resources\ApprovalMasterResource\Pages\ListApprovalMasters;
use App\Filament\Resources\ApprovalMasterResource\Pages\ViewApprovalMaster;
use App\Filament\Resources\ApprovalMasterResource\Schemas\ApprovalMasterForm;
use App\Filament\Resources\ApprovalMasterResource\Tables\ApprovalMastersTable;
use App\Models\ApprovalMaster;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ApprovalMasterResource extends Resource
{
    /*
    |--------------------------------------------------------------------------
    | Resource
    |--------------------------------------------------------------------------
    */

    protected static ?string $model = ApprovalMaster::class;

    /*
    |--------------------------------------------------------------------------
    | Slug
    |--------------------------------------------------------------------------
    */

    protected static ?string $slug = 'approval-masters';

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static UnitEnum|string|null $navigationGroup = 'Filament Shield';

    protected static ?int $navigationSort = 90;

    protected static ?string $navigationLabel = 'Approval Master';

    protected static ?string $modelLabel = 'Approval Master';

    protected static ?string $pluralModelLabel = 'Approval Masters';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedCheckBadge;

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(
        Schema $schema
    ): Schema {
        return ApprovalMasterForm::configure($schema);
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(
        Table $table
    ): Table {
        return ApprovalMastersTable::configure($table);
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\ApprovalMasterResource\RelationManagers\ApprovalStepsRelationManager::class,
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [
            'index'  => ListApprovalMasters::route('/'),
            'create' => CreateApprovalMaster::route('/create'),
            'view'   => ViewApprovalMaster::route('/{record}'),
            'edit'   => EditApprovalMaster::route('/{record}/edit'),
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
            'code',
            'name',
            'module',
            'description',
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


}