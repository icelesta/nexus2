<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions;

use App\Filament\Resources\PurchaseRequisitions\Pages\CreatePurchaseRequisition;
use App\Filament\Resources\PurchaseRequisitions\Pages\EditPurchaseRequisition;
use App\Filament\Resources\PurchaseRequisitions\Pages\ListPurchaseRequisitions;
use App\Filament\Resources\PurchaseRequisitions\Pages\ViewPurchaseRequisition;
use App\Filament\Resources\PurchaseRequisitions\Schemas\PurchaseRequisitionForm;
use App\Filament\Resources\PurchaseRequisitions\Tables\PurchaseRequisitionTable;
use App\Models\PurchaseRequisition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PurchaseRequisitionResource extends Resource
{
    protected static ?string $model = PurchaseRequisition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Material Requisition';

    protected static UnitEnum|string|null $navigationGroup = 'Purchasing';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'pr_no';

    public static function form(Schema $schema): Schema
    {
        return PurchaseRequisitionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseRequisitionTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPurchaseRequisitions::route('/'),
            'create' => CreatePurchaseRequisition::route('/create'),
            'view'   => ViewPurchaseRequisition::route('/{record}'),
            'edit'   => EditPurchaseRequisition::route('/{record}/edit'),

            // NEW
            'print'  => Pages\PrintPurchaseRequisition::route('/{record}/print'),
        ];
    }
}