<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()

                ->label('New User')

                ->icon('heroicon-o-plus')

                ->color('primary')

                ->keyBindings(['mod+n']),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Header Widgets
    |--------------------------------------------------------------------------
    */

    protected function getHeaderWidgets(): array
    {
        return [

            // Future KPI Widgets
            // Total Users
            // Active Users
            // Online Users

        ];
    }
}