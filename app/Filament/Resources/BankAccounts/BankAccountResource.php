<?php

declare(strict_types=1);

namespace App\Filament\Resources\BankAccounts;

use App\Filament\Resources\BankAccounts\Pages\CreateBankAccount;
use App\Filament\Resources\BankAccounts\Pages\EditBankAccount;
use App\Filament\Resources\BankAccounts\Pages\ListBankAccounts;
use App\Filament\Resources\BankAccounts\Schemas\BankAccountForm;
use App\Filament\Resources\BankAccounts\Tables\BankAccountsTable;
use App\Models\BankAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use Throwable;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBuildingLibrary;

    protected static ?string $navigationLabel =
        'Cash & Bank Master';

    protected static ?string $modelLabel =
        'Cash & Bank';

    protected static ?string $pluralModelLabel =
        'Cash & Bank Master';

    protected static string|\UnitEnum|null $navigationGroup =
        'Finance Setup';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'bank_name';

    public static function form(Schema $schema): Schema
    {
        return BankAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BankAccountsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBankAccounts::route('/'),
            'create' => CreateBankAccount::route('/create'),
            'edit'   => EditBankAccount::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        try {

            $table = (new (static::getModel()))->getTable();

            if (! DatabaseSchema::hasTable($table)) {
                return null;
            }

            return (string) static::getModel()::query()
                ->where('is_active', true)
                ->count();

        } catch (Throwable) {

            return null;

        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        try {

            $table = (new (static::getModel()))->getTable();

            if (! DatabaseSchema::hasTable($table)) {
                return 'gray';
            }

            $count = static::getModel()::query()->count();

            return match (true) {

                $count === 0 => 'danger',

                $count <= 10 => 'warning',

                default => 'success',

            };

        } catch (Throwable) {

            return 'gray';

        }
    }
  
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'bank_code',
            'bank_name',
            'account_name',
            'account_no',
            'branch_name',
            'swift_code',
            'iban',
        ];
    }

}