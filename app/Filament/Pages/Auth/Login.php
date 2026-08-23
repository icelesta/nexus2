<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    /*
    |--------------------------------------------------------------------------
    | CUSTOM LOGIN VIEW
    |--------------------------------------------------------------------------
    |
    | Authentication logic remains unchanged.
    | Only the presentation is replaced.
    |
    */

    protected string $view = 'filament.pages.auth.login';


    /*
    |--------------------------------------------------------------------------
    | USERNAME / EMAIL FIELD
    |--------------------------------------------------------------------------
    */

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Username / Email')
            ->placeholder('Enter username or email')
            ->required()
            ->autocomplete('username')
            ->autofocus();
    }


    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION CREDENTIALS
    |--------------------------------------------------------------------------
    */

    protected function getCredentialsFromFormData(
        array $data
    ): array {

        $login = trim(
            (string) ($data['email'] ?? '')
        );

        /*
        |--------------------------------------------------------------------------
        | EMAIL
        |--------------------------------------------------------------------------
        */

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {

            return [
                'email' => $login,
                'password' => $data['password'],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | USERNAME
        |--------------------------------------------------------------------------
        */

        return [
            'username' => $login,
            'password' => $data['password'],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | LOGIN FAILURE
    |--------------------------------------------------------------------------
    */

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' =>
                __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }
}
