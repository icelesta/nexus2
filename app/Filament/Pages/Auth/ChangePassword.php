<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePassword extends BaseEditProfile
{
    protected static ?string $title = 'Change Password';


    /*
    |--------------------------------------------------------------------------
    | PAGE WIDTH
    |--------------------------------------------------------------------------
    */

    public function getMaxContentWidth(): Width
    {
        return Width::TwoExtraLarge;
    }


    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Change Password')
                    ->description(
                        'Update your account password securely.'
                    )
                    ->schema([

                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->autocomplete('current-password'),

                        TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->same('password_confirmation')
                            ->autocomplete('new-password'),

                        TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->autocomplete('new-password'),

                    ]),

            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATE CURRENT PASSWORD
    |--------------------------------------------------------------------------
    */

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {

        $user = auth()->user();

        if (
            ! $user
            || ! Hash::check(
                $data['current_password'] ?? '',
                $user->password
            )
        ) {

            throw ValidationException::withMessages([
                'current_password' =>
                    'The current password is incorrect.',
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | Remove Current Password
        |--------------------------------------------------------------------------
        |
        | This value is only used for verification.
        | It must never be written to the users table.
        |
        */

        unset($data['current_password']);

        /*
        |--------------------------------------------------------------------------
        | Laravel User Model
        |--------------------------------------------------------------------------
        |
        | User.php already uses:
        |
        | 'password' => 'hashed'
        |
        | Therefore the new password will be hashed automatically.
        |
        */

        return $data;
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
}