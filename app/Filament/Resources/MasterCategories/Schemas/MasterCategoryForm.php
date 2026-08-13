<?php

namespace App\Filament\Resources\MasterCategories\Schemas;

use App\Models\MasterCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MasterCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('category_code')
                    ->label('Category Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),

                TextInput::make('category_name')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(200),

                Select::make('level')
                    ->label('Level')
                    ->required()
                    ->options([
                        1 => 'Category',
                        2 => 'Sub Category',
                        3 => 'Item Group',
                    ])
                    ->default(1)
                    ->live(),

                Select::make('parent_id')
                    ->label('Parent Category')
                    ->options(
                        MasterCategory::query()
                            ->orderBy('category_name')
                            ->pluck('category_name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->visible(
                        fn ($get) => in_array(
                            $get('level'),
                            [2, 3]
                        )
                    ),

                Select::make('module')
                    ->label('Module')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->default('ALL')
                    ->options([
                        'ALL'          => '🌐 All Module',

                        'Inventory'    => '📦 Inventory',
                        'Asset'        => '💼 Asset',
                        'Finance'      => '💰 Finance',
                        'Project'      => '📁 Project',
                        'Quality'      => '✅ Quality',
                        'Maintenance'  => '🔧 Maintenance',

                        'Procurement'  => '🛒 Procurement',
                        'Sales'        => '📈 Sales',
                        'HR'           => '👥 Human Resource',

                        'Production'   => '🏭 Production',
                        'CRM'          => '🤝 CRM',
                        'Document'     => '📄 Document',
                        'System'       => '⚙️ System',
                    ])
                    ->helperText(
                        'ALL Module = Category tersedia untuk seluruh module Nexus ERP'
                    ),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

            ]);
    }
}