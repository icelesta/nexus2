<?php

namespace App\Filament\Resources\Sections\Schemas;

use App\Models\Branch;
use App\Models\BusinessUnit;
use App\Models\Company;
use App\Models\Department;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)

            ->components([

                Select::make('company_id')
                    ->label('Company')
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
                    ->label('Branch')
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
                    ->label('Business Unit')
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

                Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'department_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('section_code')
                    ->label('Section Code')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),

                TextInput::make('section_name')
                    ->label('Section Name')
                    ->required()
                    ->maxLength(255),

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