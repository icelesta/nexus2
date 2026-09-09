<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentDirectMarketResource;

use App\Filament\Resources\AssignmentDirectMarketResource\Pages\EditAssignmentDirectMarket;
use App\Filament\Resources\AssignmentDirectMarketResource\Pages\ListAssignmentDirectMarkets;
use App\Filament\Resources\AssignmentDirectMarketResource\Pages\ViewAssignmentDirectMarket;
use App\Filament\Resources\AssignmentDirectMarketResource\Schemas\AssignmentDirectMarketForm;
use App\Filament\Resources\AssignmentDirectMarketResource\Schemas\AssignmentDirectMarketInfolist;
use App\Filament\Resources\AssignmentDirectMarketResource\Tables\AssignmentDirectMarketsTable;
use App\Models\AssignmentDirectMarket;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class AssignmentDirectMarketResource extends Resource
{
    protected static ?string $model =
        AssignmentDirectMarket::class;

    protected static UnitEnum|string|null $navigationGroup =
        'Purchasing';

    protected static ?int $navigationSort =
        45;

    protected static ?string $navigationLabel =
        'Assignment DM';

    protected static ?string $navigationParentItem = 'Direct Market';

    protected static ?string $modelLabel =
        'Assignment Direct Market';

    protected static ?string $pluralModelLabel =
        'Assignment Direct Markets';

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-clipboard-document-list';

    protected static ?string $slug =
        'assignment-direct-markets';

    public static function form(
        Schema $schema
    ): Schema {
        return AssignmentDirectMarketForm::configure(
            $schema
        );
    }

    public static function infolist(
        Schema $schema
    ): Schema {
        return AssignmentDirectMarketInfolist::configure(
            $schema
        );
    }

    public static function table(
        Table $table
    ): Table {
        return AssignmentDirectMarketsTable::configure(
            $table
        );
    }

    public static function getPages(): array
    {
        return [

            'index' =>
                ListAssignmentDirectMarkets::route('/'),

            'view' =>
                ViewAssignmentDirectMarket::route(
                    '/{record}'
                ),

            'edit' =>
                EditAssignmentDirectMarket::route(
                    '/{record}/edit'
                ),

        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}