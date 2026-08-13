<?php

namespace App\Filament\Resources\MasterCategories;


use App\Filament\Resources\MasterCategories\Pages\CreateMasterCategory;
use App\Filament\Resources\MasterCategories\Pages\EditMasterCategory;
use App\Filament\Resources\MasterCategories\Pages\ListMasterCategories;
use App\Filament\Resources\MasterCategories\Schemas\MasterCategoryForm;
use App\Filament\Resources\MasterCategories\Tables\MasterCategoriesTable;
use App\Models\MasterCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterCategoryResource extends Resource
{
    protected static ?string $model = MasterCategory::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedTag;

    protected static ?string $navigationLabel =
        'Category Management';

    protected static ?string $modelLabel =
        'Category';

    protected static ?string $pluralModelLabel =
        'Category Management';

    protected static string|\UnitEnum|null $navigationGroup =
        'Setup Master';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute =
        'category_name';

    public static function form(Schema $schema): Schema
    {
        return MasterCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [

            'index'  => ListMasterCategories::route('/'),

            'create' => CreateMasterCategory::route('/create'),

            'edit'   => EditMasterCategory::route('/{record}/edit'),

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

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'category_code',
            'category_name',
            'category_type',
            'module',
            'short_name',
        ];
    }    

}