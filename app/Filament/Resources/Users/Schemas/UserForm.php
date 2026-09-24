<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;

use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | User Information
                |--------------------------------------------------------------------------
                */

                Section::make('User Information')
                    ->description('General account information.')
                    ->columns(2)
                    ->schema([

                        TextInput::make('employee_no')
                            ->label('Employee No')
                            ->placeholder('EMP-000001')
                            ->maxLength(50),

                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->autocomplete('name')
                            ->maxLength(255),

                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->alphaDash()
                            ->minLength(3)
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->autocomplete('username'),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->autocomplete('email')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+628123456789')
                            ->prefixIcon('heroicon-o-phone'),

                        FileUpload::make('avatar')
                            ->label('Profile Photo')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('avatars')
                            ->visibility('public')
                            ->getUploadedFileUsing(
                                function (
                                    FileUpload $component,
                                    string $file,
                                    string|array|null $storedFileNames
                                ): ?array {

                                    $uploadedFile = $component->getUploadedFile(
                                        $file,
                                        $storedFileNames
                                    );

                                    if ($uploadedFile === null) {
                                        return null;
                                    }

                                    $uploadedFile['url'] = asset(
                                        'storage/' . ltrim($file, '/')
                                    );

                                    return $uploadedFile;
                                }
                            )
                            ->maxSize(2048)
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ])
                            ->avatar()
                            ->columnSpanFull(),
                            

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation) => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(
                                fn ($state) => filled($state)
                                    ? Hash::make($state)
                                    : null
                            )
                            ->afterStateHydrated(
                                fn (TextInput $component) => $component->state('')
                            )
                            ->placeholder('Leave blank when editing'),

                        Select::make('locale')
                            ->label('Language')
                            ->options([
                                'id'    => 'Bahasa Indonesia',
                                'en'    => 'English',
                                'ms'    => 'Bahasa Melayu',
                                'zh_CN' => 'Chinese (Simplified)',
                                'zh_TW' => 'Chinese (Traditional)',
                                'ja'    => 'Japanese',
                                'ko'    => 'Korean',
                                'ar'    => 'Arabic',
                            ])
                            ->default('id')
                            ->searchable()
                            ->native(false)
                            ->required(),

                        Select::make('timezone')
                            ->label('Time Zone')
                            ->options([
                                'Asia/Jakarta'      => 'Asia / Jakarta',
                                'Asia/Makassar'     => 'Asia / Makassar',
                                'Asia/Jayapura'     => 'Asia / Jayapura',
                                'Asia/Singapore'    => 'Asia / Singapore',
                                'Asia/Kuala_Lumpur' => 'Asia / Kuala Lumpur',
                                'UTC'               => 'UTC',
                            ])
                            ->default('Asia/Jakarta')
                            ->searchable()
                            ->native(false)
                            ->required(),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Organization Structure
                |--------------------------------------------------------------------------
                */

                Section::make('Organization Structure')
                    ->description('Assign organization hierarchy.')
                    ->columns(2)
                    ->schema([

                        Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('branch_id')
                            ->label('Branch')
                            ->relationship('branch', 'branch_name')
                            ->searchable()
                            ->preload(),

                        Select::make('business_unit_id')
                            ->label('Business Unit')
                            ->relationship('businessUnit', 'business_unit_name')
                            ->searchable()
                            ->preload(),

                        Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'department_name')
                            ->searchable()
                            ->preload(),

                        Select::make('section_id')
                            ->label('Section')
                            ->relationship('section', 'section_name')
                            ->searchable()
                            ->preload(),

                        Select::make('cost_center_id')
                            ->label('Cost Center')
                            ->relationship('costCenter', 'cost_center_name')
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => "{$record->cost_center_code} - {$record->cost_center_name}"
                            ),

                        Select::make('profit_center_id')
                            ->label('Profit Center')
                            ->relationship('profitCenter', 'profit_center_name')
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => "{$record->profit_center_code} - {$record->profit_center_name}"
                            ),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | Transaction Filter
                |--------------------------------------------------------------------------
                */

                Section::make('Transaction Filter')
                    ->description('Configure default transaction list filter behavior.')
                    ->schema([

                        Toggle::make('global_filter_all_departments')
                            ->label('Default Global Filter: All Departments')
                            ->helperText(
                                'When enabled, Material Requisition and Direct Market lists open with All Departments selected by default.'
                            )
                            ->default(false),

                    ]),



                /*
                |--------------------------------------------------------------------------
                | Security
                |--------------------------------------------------------------------------
                */

                Section::make('Security')
                    ->description('Assign application roles.')
                    ->schema([

                        CheckboxList::make('roles')
                            ->relationship('roles', 'name')
                            ->columns(3)
                            ->searchable()
                            ->bulkToggleable(),

                    ]),                   

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                Section::make('Status')
                    ->schema([

                        Toggle::make('is_active')
                            ->label('Active User')
                            ->default(true),

                    ]),

            ]);
    }
}