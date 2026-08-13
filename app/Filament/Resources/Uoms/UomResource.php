<?php

declare(strict_types=1);

namespace App\Filament\Resources\Uoms;

use App\Filament\Resources\Uoms\Pages\CreateUom;
use App\Filament\Resources\Uoms\Pages\EditUom;
use App\Filament\Resources\Uoms\Pages\ListUoms;
use App\Filament\Resources\Uoms\Schemas\UomForm;
use App\Filament\Resources\Uoms\Tables\UomsTable;
use App\Models\Uom;

use BackedEnum;
use UnitEnum;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UomResource extends Resource
{
    protected static ?string $model = Uom::class;

    protected static ?string $recordTitleAttribute = 'uom_name';

    protected static ?string $navigationLabel = 'Unit of Measure';

    protected static ?string $modelLabel = 'Unit of Measure';

    protected static ?string $pluralModelLabel = 'Units of Measure';

    protected static string|UnitEnum|null $navigationGroup = 'Inventory Setup';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    public static function form(Schema $schema): Schema
    {
        return UomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UomsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUoms::route('/'),
            'create' => CreateUom::route('/create'),
            'edit'   => EditUom::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'uom_code',
            'uom_name',
            'symbol',
            'category',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "{$record->uom_code} - {$record->uom_name}";
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Uom::query()
            ->active()
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}