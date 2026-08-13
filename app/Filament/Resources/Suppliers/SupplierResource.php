<?php

declare(strict_types=1);

namespace App\Filament\Resources\Suppliers;

use App\Filament\Resources\Suppliers\Pages\CreateSupplier;
use App\Filament\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\Resources\Suppliers\Pages\ListSuppliers;

use App\Filament\Resources\Suppliers\Schemas\SupplierForm;
use App\Filament\Resources\Suppliers\Tables\SuppliersTable;

use App\Models\Supplier;

use BackedEnum;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;

class SupplierResource extends Resource
{
    /*
    |--------------------------------------------------------------------------
    | Resource
    |--------------------------------------------------------------------------
    */

    protected static ?string $model =
        Supplier::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBuildingOffice2;

    protected static string|\UnitEnum|null $navigationGroup =
        'Setup Master';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel =
        'Supplier Master';

    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    */

    protected static ?string $modelLabel =
        'Supplier';

    protected static ?string $pluralModelLabel =
        'Suppliers';

    protected static ?string $recordTitleAttribute =
        'display_name';

    protected static ?string $slug =
        'suppliers';

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(
        Schema $schema,
    ): Schema {

        return SupplierForm::configure(
            $schema
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(
        Table $table,
    ): Table {

        return SuppliersTable::configure(
            $table
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public static function getRelations(): array
    {
        return [

            //

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation Badge
    |--------------------------------------------------------------------------
    */

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()

            ->where('is_active', true)

            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::query()

            ->where('is_active', true)

            ->count();

        return match (true) {

            $count === 0 => 'danger',

            $count <= 10 => 'warning',

            default => 'success',

        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active Suppliers';
    }


    /*
    |--------------------------------------------------------------------------
    | Global Search
    |--------------------------------------------------------------------------
    */

    public static function getGloballySearchableAttributes(): array
    {
        return [

            'supplier_code',

            'supplier_name',

            'contact_person',

            'email',

            'phone',

            'mobile',

            'website',

            'city',

            'province',

            'company_type',

            'bank_name',

            'bank_account_name',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Global Search Result
    |--------------------------------------------------------------------------
    */

    public static function getGlobalSearchResultTitle(
        $record,
    ): string {

        return $record->display_name;
    }

    public static function getGlobalSearchResultDetails(
        \Illuminate\Database\Eloquent\Model $record,
    ): array {

        return [

            'Company' => $record->company?->company_name ?? '-',

            'Category' => $record->category?->category_name ?? '-',

            'Contact' => $record->contact_person ?? '-',

            'Phone' => $record->phone ?: $record->mobile ?: '-',

            'Email' => $record->email ?? '-',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Eloquent Query
    |--------------------------------------------------------------------------
    */

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()

            ->with([

                'company',

                'category',

                'currency',

                'paymentTerm',

                'creator',

                'updater',

            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [

            'index'  => ListSuppliers::route('/'),

            'create' => CreateSupplier::route('/create'),

            'edit'   => EditSupplier::route('/{record}/edit'),

        ];
    }
}