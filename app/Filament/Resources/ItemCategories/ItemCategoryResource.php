<?php

declare(strict_types=1);

namespace App\Filament\Resources\ItemCategories;

use App\Filament\Resources\ItemCategories\Pages\CreateItemCategory;
use App\Filament\Resources\ItemCategories\Pages\EditItemCategory;
use App\Filament\Resources\ItemCategories\Pages\ListItemCategories;

use App\Filament\Resources\ItemCategories\Schemas\ItemCategoryForm;
use App\Filament\Resources\ItemCategories\Tables\ItemCategoriesTable;

use App\Models\ItemCategory;

use BackedEnum;
use UnitEnum;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemCategoryResource extends Resource
{
    /**
     * --------------------------------------------------------------------------
     * Model
     * --------------------------------------------------------------------------
     */

    protected static ?string $model = ItemCategory::class;

    protected static ?string $recordTitleAttribute = 'category_name';

    /**
     * --------------------------------------------------------------------------
     * Navigation
     * --------------------------------------------------------------------------
     */

    protected static ?string $navigationLabel = 'Item Categories';

    protected static ?string $modelLabel = 'Item Category';

    protected static ?string $pluralModelLabel = 'Item Categories';

    protected static string|UnitEnum|null $navigationGroup = 'Inventory Setup';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    /**
     * --------------------------------------------------------------------------
     * Form
     * --------------------------------------------------------------------------
     */

    public static function form(Schema $schema): Schema
    {
        return ItemCategoryForm::configure($schema);
    }

    /**
     * --------------------------------------------------------------------------
     * Table
     * --------------------------------------------------------------------------
     */

    public static function table(Table $table): Table
    {
        return ItemCategoriesTable::configure($table);
    }

    /**
     * --------------------------------------------------------------------------
     * Infolist
     * --------------------------------------------------------------------------
     */



    /**
     * --------------------------------------------------------------------------
     * Relations
     * --------------------------------------------------------------------------
     */

    public static function getRelations(): array
    {
        return [];
    }

    /**
     * --------------------------------------------------------------------------
     * Pages
     * --------------------------------------------------------------------------
     */

    public static function getPages(): array
    {
        return [
            'index' => ListItemCategories::route('/'),
            'create' => CreateItemCategory::route('/create'),
            'edit' => EditItemCategory::route('/{record}/edit'),
        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Query
     * --------------------------------------------------------------------------
     */

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    /**
     * --------------------------------------------------------------------------
     * Global Search
     * --------------------------------------------------------------------------
     */

    public static function getGloballySearchableAttributes(): array
    {
        return [

            'category_code',

            'category_name',

        ];
    }

    public static function getGlobalSearchResultTitle(
        Model $record,
    ): string {

        return "{$record->category_code} - {$record->category_name}";
    }

    /**
     * --------------------------------------------------------------------------
     * Navigation Badge
     * --------------------------------------------------------------------------
     */

    public static function getNavigationBadge(): ?string
    {
        return (string) ItemCategory::query()
            ->active()
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}