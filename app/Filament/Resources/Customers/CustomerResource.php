<?php

namespace App\Filament\Resources\Customers;

use App\Filament\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\Customers\Pages\ListCustomers;


use App\Filament\Resources\Customers\Schemas\CustomerForm;
use App\Filament\Resources\Customers\Tables\CustomersTable;

use App\Models\Customer;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model =
        Customer::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute =
        'customer_name';

    protected static string|\UnitEnum|null $navigationGroup =
        'Setup Master';

    protected static ?int $navigationSort = 40;

    protected static ?string $navigationLabel =
        'Customer Master';

    protected static ?string $modelLabel =
        'Customer';

    protected static ?string $pluralModelLabel =
        'Customer Master';

    protected static ?string $slug =
        'customers';

    public static function form(Schema $schema): Schema
    {
        return CustomerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'customer_code',
            'customer_name',
            'legal_name',
            'short_name',
            'customer_type',
            'industry',
            'tax_number',
            'business_license',
            'email',
            'phone',
            'mobile',
            'website',
            'city',
            'province',
            'pic_name',
            'pic_position',
        ];
    }

    public static function getGlobalSearchResultTitle(
        $record
    ): string {

        return "{$record->customer_code} - {$record->customer_name}";
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [

            'index' =>
                ListCustomers::route('/'),

            'create' =>
                CreateCustomer::route('/create'),

            'edit' =>
                EditCustomer::route('/{record}/edit'),

        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->where('is_active', true)
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();

        return match (true) {

            $count === 0 => 'danger',

            $count <= 10 => 'warning',

            default => 'success',
        };
    }
}