<?php

declare(strict_types=1);

namespace App\Filament\Resources\DirectMarkets;

use App\Filament\Resources\DirectMarkets\Pages\PrintDirectMarket;
use App\Filament\Resources\DirectMarkets\Pages\CreateDirectMarket;
use App\Filament\Resources\DirectMarkets\Pages\EditDirectMarket;
use App\Filament\Resources\DirectMarkets\Pages\ListDirectMarkets;
use App\Filament\Resources\DirectMarkets\Pages\ViewDirectMarket;
use App\Filament\Resources\DirectMarkets\Schemas\DirectMarketForm;
use App\Filament\Resources\DirectMarkets\Schemas\DirectMarketView;
use App\Filament\Resources\DirectMarkets\Tables\DirectMarketTable;
use App\Models\DirectMarket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class DirectMarketResource extends Resource
{
    protected static ?string $model =
        DirectMarket::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel =
        'Create DM';

    protected static ?string $navigationParentItem = 'Direct Market';

    protected static UnitEnum|string|null $navigationGroup =
        'Purchasing';

    protected static ?int $navigationSort =
        2;

    protected static ?string $recordTitleAttribute =
        'dm_no';

    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public static function form(
        Schema $schema
    ): Schema {

        return DirectMarketForm::configure(
            $schema
        );
    }

    /*
    |--------------------------------------------------------------------------
    | INFOLIST
    |--------------------------------------------------------------------------
    */

    public static function infolist(
        Schema $schema
    ): Schema {

        return DirectMarketView::configure(
            $schema
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    public static function table(
        Table $table
    ): Table {

        return DirectMarketTable::configure(
            $table
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [

            'index' =>
                ListDirectMarkets::route('/'),

            'create' =>
                CreateDirectMarket::route('/create'),

            'view' =>
                ViewDirectMarket::route('/{record}'),

            'edit' =>
                EditDirectMarket::route('/{record}/edit'),

            'print' =>
                PrintDirectMarket::route('/{record}/print'),
        ];
    }
}
