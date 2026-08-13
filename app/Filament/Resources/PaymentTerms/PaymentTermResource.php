<?php

namespace App\Filament\Resources\PaymentTerms;

use App\Filament\Resources\PaymentTerms\Pages\CreatePaymentTerm;
use App\Filament\Resources\PaymentTerms\Pages\EditPaymentTerm;
use App\Filament\Resources\PaymentTerms\Pages\ListPaymentTerms;
use App\Filament\Resources\PaymentTerms\Schemas\PaymentTermForm;
use App\Filament\Resources\PaymentTerms\Tables\PaymentTermsTable;

use App\Models\PaymentTerm;

use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use Throwable;

class PaymentTermResource extends Resource
{
    protected static ?string $model = PaymentTerm::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedCreditCard;

    protected static string|\UnitEnum|null $navigationGroup =
        'Finance Setup';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Payment Terms';

    protected static ?string $modelLabel = 'Payment Term';

    protected static ?string $pluralModelLabel = 'Payment Terms';

    protected static ?string $slug = 'payment-terms';

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function form(Schema $schema): Schema
    {
        return PaymentTermForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentTermsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    

    public static function getPages(): array
    {
        return [

            'index'  => ListPaymentTerms::route('/'),
            'create' => CreatePaymentTerm::route('/create'),
            'edit'   => EditPaymentTerm::route('/{record}/edit'),

        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()

            ->with([

                'company',
                'creator',
                'updater',

            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [

            'term_code',
            'term_name',
            'description',
            'company.company_name',

        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->display_name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [

            'Company' => $record->company?->company_name,

            'Due Days' => "{$record->due_days} Days",

            'Discount' => "{$record->discount_percent}%",

        ];
    }

    public static function getNavigationBadge(): ?string
    {
        try {

            $table = (new (static::getModel()))->getTable();

            if (! DatabaseSchema::hasTable($table)) {
                return null;
            }

            return (string) static::getModel()::active()->count();

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

            $count = static::getModel()::active()->count();

            return match (true) {

                $count === 0 => 'danger',

                $count <= 10 => 'warning',

                default => 'success',

            };

        } catch (Throwable) {

            return 'gray';

        }
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active Payment Terms';
    }
}