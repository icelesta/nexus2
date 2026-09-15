<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionNumberings;

use App\Filament\Resources\TransactionNumberings\Pages\CreateTransactionNumbering;
use App\Filament\Resources\TransactionNumberings\Pages\EditTransactionNumbering;
use App\Filament\Resources\TransactionNumberings\Pages\ListTransactionNumberings;
use App\Filament\Resources\TransactionNumberings\Schemas\TransactionNumberingForm;
use App\Filament\Resources\TransactionNumberings\Tables\TransactionNumberingsTable;
use App\Models\TransactionNumbering;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionNumberingResource extends Resource
{
    protected static ?string $model = TransactionNumbering::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedIdentification;

    protected static string|\UnitEnum|null $navigationGroup =
        'Setup Master';

    protected static ?int $navigationSort = 30;

    protected static ?string $navigationLabel =
        'Transaction Numbering';

    protected static ?string $modelLabel =
        'Transaction Numbering';

    protected static ?string $pluralModelLabel =
        'Transaction Numbering';

    protected static ?string $recordTitleAttribute =
        'document_name';

    protected static ?string $slug =
        'transaction-numberings';

    public static function form(Schema $schema): Schema
    {
        return TransactionNumberingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionNumberingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [

            'module',

            'document_type',

            'document_name',

            'prefix',

            'suffix',

            'remarks',

            'format_pattern',

        ];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return "{$record->document_type} - {$record->document_name}";
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->ordered();
    }

    public static function getPages(): array
    {
        return [

            'index'  => ListTransactionNumberings::route('/'),

            'create' => CreateTransactionNumbering::route('/create'),

            'edit'   => EditTransactionNumbering::route('/{record}/edit'),

           /* 'view' => ViewTransactionNumbering::route('/{record}'),*/

        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()
            ::query()
            ->where('is_active', true)
            ->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::query()
            ->active()
            ->count();

        return match (true) {

            $count === 0 => 'danger',

            $count <= 10 => 'warning',

            default => 'success',

        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Configured Active Transaction Numberings';
    }

    /**
     * Authorization is intentionally delegated to TransactionNumberingPolicy.
     * Do not bypass Role permissions at the Resource level.
     */

}