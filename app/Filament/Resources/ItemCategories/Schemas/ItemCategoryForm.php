<?php

declare(strict_types=1);

namespace App\Filament\Resources\ItemCategories\Schemas;

use App\Models\ItemCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItemCategoryForm
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema

            ->components([

                /*
                |--------------------------------------------------------------------------
                | Category Information
                |--------------------------------------------------------------------------
                */

                TextInput::make('category_code')
                    ->label('Category Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(30)
                    ->autocomplete(false),

                TextInput::make('category_name')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(150),

                Select::make('parent_id')
                    ->label('Parent Category')
                    ->relationship(
                        'parent',
                        'category_name'
                    )
                    ->searchable()
                    ->preload()
                    ->placeholder('Root Category'),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Configuration
                |--------------------------------------------------------------------------
                */

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

            ]);

    }
}