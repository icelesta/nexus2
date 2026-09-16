<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts;

use App\Filament\Resources\GoodsReceipts\Pages\ViewGoodsReceipt;
use App\Filament\Resources\GoodsReceipts\Schemas\GoodsReceiptInfolist;
use App\Filament\Resources\GoodsReceipts\Tables\GoodsReceiptsTable;

use App\Filament\Resources\GoodsReceipts\Pages\ListGoodsReceipts;
use App\Models\GoodsReceipt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class GoodsReceiptResource extends Resource
{
    /*
    |--------------------------------------------------------------------------
    | Resource
    |--------------------------------------------------------------------------
    */

    protected static ?string $model =
        GoodsReceipt::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static UnitEnum|string|null $navigationGroup =
        'Logistics';

    protected static ?int $navigationSort =
        10;

    protected static ?string $navigationLabel =
        'Receiving Record';

    protected static ?string $modelLabel =
        'Receiving Record';

    protected static ?string $pluralModelLabel =
        'Receiving Records';

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-clipboard-document-check';

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(
        Schema $schema
    ): Schema {

        return $schema;
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(
        Table $table
    ): Table {

        return GoodsReceiptsTable::configure(
            $table
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Infolist
    |--------------------------------------------------------------------------
    */

    public static function infolist(
        Schema $schema
    ): Schema {

        return GoodsReceiptInfolist::configure(
            $schema
        );
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
                ListGoodsReceipts::route('/'),

            'view' =>
                ViewGoodsReceipt::route('/{record}'),

        ];
    }


}