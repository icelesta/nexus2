<?php

namespace App\Filament\Resources\ChartOfAccounts;

use App\Filament\Resources\ChartOfAccounts\Pages\CreateChartOfAccount;
use App\Filament\Resources\ChartOfAccounts\Pages\EditChartOfAccount;
use App\Filament\Resources\ChartOfAccounts\Pages\ListChartOfAccounts;
use App\Filament\Resources\ChartOfAccounts\Schemas\ChartOfAccountForm;
use App\Filament\Resources\ChartOfAccounts\Tables\ChartOfAccountsTable;
use App\Models\ChartOfAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccountResource extends Resource
{
    protected static ?string $model = ChartOfAccount::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBuildingLibrary;

    protected static string|\UnitEnum|null $navigationGroup =
        'Finance Setup';

    protected static ?string $navigationLabel =
        'Chart of Accounts';

    protected static ?string $modelLabel =
        'Chart of Account';

    protected static ?string $pluralModelLabel =
        'Chart of Accounts';

    protected static ?int $navigationSort = 4;

    /*
    |--------------------------------------------------------------------------
    | Record Title
    |--------------------------------------------------------------------------
    */

    protected static ?string $recordTitleAttribute = 'account_name';

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(Schema $schema): Schema
    {
        return ChartOfAccountForm::configure($schema);
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return ChartOfAccountsTable::configure($table);
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public static function getRelations(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [
            'index'  => ListChartOfAccounts::route('/'),
            'create' => CreateChartOfAccount::route('/create'),
            'edit'   => EditChartOfAccount::route('/{record}/edit'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'company',
                'parent',
                'currency',
                'creator',
                'updater',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Global Search
    |--------------------------------------------------------------------------
    */

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'account_code',
            'account_name',
            'account_type',
            'normal_balance',
        ];
    }

    public static function getGlobalSearchResultDetails(
        Model $record
    ): array {
        return [
            'Code'            => $record->account_code,
            'Account Name'    => $record->account_name,
            'Account Type'    => $record->account_type,
            'Company'         => $record->company?->company_name,
            'Currency'        => $record->currency?->currency_code,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation Badge
    |--------------------------------------------------------------------------
    */

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()
            ::active()
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()
            ::active()
            ->count();

        return match (true) {
            $count === 0 => 'danger',
            $count <= 10 => 'warning',
            default => 'success',
        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active Chart of Accounts';
    }
}