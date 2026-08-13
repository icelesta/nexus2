<?php

namespace App\Filament\Resources\JournalTypes;

use App\Filament\Resources\JournalTypes\Pages\CreateJournalType;
use App\Filament\Resources\JournalTypes\Pages\EditJournalType;
use App\Filament\Resources\JournalTypes\Pages\ListJournalTypes;
use App\Filament\Resources\JournalTypes\Schemas\JournalTypeForm;
use App\Filament\Resources\JournalTypes\Tables\JournalTypesTable;

use App\Models\JournalType;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JournalTypeResource extends Resource
{
    protected static ?string $model = JournalType::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup =
        'Finance Setup';

    protected static ?string $navigationLabel =
        'Journal Types';

    protected static ?string $modelLabel =
        'Journal Type';

    protected static ?string $pluralModelLabel =
        'Journal Types';

    protected static ?int $navigationSort = 10;

    /*
    |--------------------------------------------------------------------------
    | Record Title
    |--------------------------------------------------------------------------
    */

    protected static ?string $recordTitleAttribute =
        'display_name';

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(
        Schema $schema
    ): Schema {
        return JournalTypeForm::configure($schema);
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(
        Table $table
    ): Table {
        return JournalTypesTable::configure($table);
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

            'index' => ListJournalTypes::route('/'),

            'create' => CreateJournalType::route('/create'),

            'edit' => EditJournalType::route('/{record}/edit'),

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

            'code',

            'name',

            'description',

        ];
    }

    public static function getGlobalSearchResultDetails(
        Model $record
    ): array {

        return [

            'Code' => $record->code,

            'Name' => $record->name,

            'Status' => $record->is_active
                ? 'Active'
                : 'Inactive',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation Badge
    |--------------------------------------------------------------------------
    */

/*    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::active()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active Journal Types';
    }*/

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    
}