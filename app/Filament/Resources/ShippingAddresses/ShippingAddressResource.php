<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingAddresses;

use App\Filament\Resources\ShippingAddresses\Pages\CreateShippingAddress;
use App\Filament\Resources\ShippingAddresses\Pages\EditShippingAddress;
use App\Filament\Resources\ShippingAddresses\Pages\ListShippingAddresses;
use App\Filament\Resources\ShippingAddresses\Schemas\ShippingAddressForm;
use App\Filament\Resources\ShippingAddresses\Tables\ShippingAddressesTable;
use App\Models\ShippingAddress;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ShippingAddressResource extends Resource
{
    protected static ?string $model = ShippingAddress::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup =
        'Inventory Setup';

    protected static ?int $navigationSort = 40;

    protected static ?string $navigationLabel =
        'Shipping Address';

    protected static ?string $modelLabel =
        'Shipping Address';

    protected static ?string $pluralModelLabel =
        'Shipping Addresses';

    protected static ?string $recordTitleAttribute =
        'shipping_name';

    public static function form(Schema $schema): Schema
    {
        return ShippingAddressForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShippingAddressesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShippingAddresses::route('/'),
            'create' => CreateShippingAddress::route('/create'),
            'edit' => EditShippingAddress::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::$model::query()->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'shipping_code',
            'shipping_name',
            'city',
            'province',
            'contact_person',
        ];
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