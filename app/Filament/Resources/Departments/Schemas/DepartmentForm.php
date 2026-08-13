<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('company_id')
                    ->relationship(
                        name: 'company',
                        titleAttribute: 'company_name',
                        modifyQueryUsing: function ($query, $record) {

                            $query->where(function ($q) use ($record) {

                                $q->where('is_active', true);

                                if ($record?->company_id) {
                                    $q->orWhere('id', $record->company_id);
                                }

                            });

                        },
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('branch_id')
                    ->relationship(
                        name: 'branch',
                        titleAttribute: 'branch_name',
                        modifyQueryUsing: function ($query, $record) {

                            $query->where(function ($q) use ($record) {

                                $q->where('is_active', true);

                                if ($record?->branch_id) {
                                    $q->orWhere('id', $record->branch_id);
                                }

                            });

                        },
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('business_unit_id')
                    ->relationship(
                        name: 'businessUnit',
                        titleAttribute: 'business_unit_name',
                        modifyQueryUsing: function ($query, $record) {

                            $query->where(function ($q) use ($record) {

                                $q->where('is_active', true);

                                if ($record?->business_unit_id) {
                                    $q->orWhere('id', $record->business_unit_id);
                                }

                            });

                        },
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('department_code')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('department_name')
                    ->required(),

                Textarea::make('description')
                    ->rows(3),

                Toggle::make('is_active')
                    ->default(true),

            ]);
    }
}