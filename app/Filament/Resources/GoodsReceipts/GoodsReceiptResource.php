<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts;

use App\Filament\Resources\GoodsReceipts\Pages\CreateGoodsReceipt;
use App\Filament\Resources\GoodsReceipts\Pages\ListGoodsReceipts;
use App\Filament\Resources\GoodsReceipts\Pages\ViewGoodsReceipt;
use App\Filament\Resources\GoodsReceipts\Schemas\GoodsReceiptForm;
use App\Filament\Resources\GoodsReceipts\Schemas\GoodsReceiptInfolist;
use App\Filament\Resources\GoodsReceipts\Tables\GoodsReceiptsTable;
use App\Filament\Resources\GoodsReceipts\Pages\EditGoodsReceipt;


use App\Models\GoodsReceipt;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;
use BackedEnum;

class GoodsReceiptResource extends Resource
{
    protected static ?string $model = GoodsReceipt::class;

    protected static UnitEnum|string|null $navigationGroup = 'Logistics';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationLabel = 'Receiving Record';
    protected static ?string $modelLabel = 'Receiving Record';
    protected static ?string $pluralModelLabel = 'Receiving Records';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Schema $schema): Schema
    {
        return GoodsReceiptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GoodsReceiptsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GoodsReceiptInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [

            'index' =>
                ListGoodsReceipts::route('/'),

            'create' =>
                CreateGoodsReceipt::route('/create'),

            'view' =>
                ViewGoodsReceipt::route('/{record}'),

            'edit' =>
                EditGoodsReceipt::route('/{record}/edit'),

        ];
    }
}
