<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\Width;

class EditProfile extends BaseEditProfile
{
    protected static ?string $title = 'My Profile';

    /*
    |--------------------------------------------------------------------------
    | PAGE WIDTH
    |--------------------------------------------------------------------------
    */

    public function getMaxContentWidth(): Width
    {
        return Width::SevenExtraLarge;
    }

    /*
    |--------------------------------------------------------------------------
    | REDIRECT AFTER SAVE
    |--------------------------------------------------------------------------
    */

    protected function getRedirectUrl(): string
    {
        return filament()->getUrl();
    }

    /*
    |--------------------------------------------------------------------------
    | PROFILE FORM
    |--------------------------------------------------------------------------
    */

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | TWO COLUMN PROFILE LAYOUT
                |--------------------------------------------------------------------------
                */

                Grid::make([
                    'default' => 1,
                    'lg' => 2,
                ])
                    ->schema([


                        /*
                        |--------------------------------------------------------------------------
                        | PERSONAL INFORMATION
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Personal Information')
                            ->description(
                                'Update your personal account information.'
                            )
                            ->columns(2)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | PROFILE PHOTO
                                |--------------------------------------------------------------------------
                                */

                                FileUpload::make('avatar')
                                    ->label('Profile Photo')
                                    ->avatar()
                                    ->image()
                                    ->imageEditor()
                                    ->directory('users')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->imageResizeMode('cover')
                                    ->acceptedFileTypes([
                                        'image/jpeg',
                                        'image/png',
                                        'image/webp',
                                    ])
                                    ->imageResizeTargetWidth('400')
                                    ->imageResizeTargetHeight('400')
                                    ->maxSize(2048)
                                    ->columnSpanFull(),                                

                                /*
                                |--------------------------------------------------------------------------
                                | COMPANY
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make('company')
                                    ->label('Company')
                                    ->formatStateUsing(
                                        fn () =>
                                            auth()->user()?->company?->display_name
                                            ?? '-'
                                    )
                                    ->disabled()
                                    ->dehydrated(false),


                                /*
                                |--------------------------------------------------------------------------
                                | DEPARTMENT
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make('department')
                                    ->label('Department')
                                    ->formatStateUsing(
                                        fn () =>
                                            auth()->user()?->department?->display_name
                                            ?? '-'
                                    )
                                    ->disabled()
                                    ->dehydrated(false),


                                /*
                                |--------------------------------------------------------------------------
                                | EMPLOYEE NO
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make('employee_no')
                                    ->label('Employee No')
                                    ->disabled()
                                    ->dehydrated(false),


                                /*
                                |--------------------------------------------------------------------------
                                | USERNAME
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make('username')
                                    ->label('Username')
                                    ->disabled()
                                    ->dehydrated(false),


                                /*
                                |--------------------------------------------------------------------------
                                | FULL NAME
                                |--------------------------------------------------------------------------
                                */

                                $this->getNameFormComponent()
                                    ->label('Full Name'),


                                /*
                                |--------------------------------------------------------------------------
                                | EMAIL
                                |--------------------------------------------------------------------------
                                */

                                $this->getEmailFormComponent()
                                    ->label('Email'),


                                /*
                                |--------------------------------------------------------------------------
                                | PHONE
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('+628123456789')
                                    ->columnSpanFull(),

                            ]),


                        /*
                        |--------------------------------------------------------------------------
                        | SECURITY
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Security')
                            ->description(
                                'Manage your account password.'
                            )
                            ->columns(1)
                            ->schema([

                                $this->getPasswordFormComponent()
                                    ->label('New Password'),

                                $this->getPasswordConfirmationFormComponent()
                                    ->label('Confirm New Password'),

                            ]),

                    ]),

            ]);
    }
}