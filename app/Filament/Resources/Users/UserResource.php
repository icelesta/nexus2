<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;

use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;

use App\Models\User;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup =
        'Filament Shield';

    protected static ?string $navigationLabel =
        'Users';

    protected static ?string $modelLabel =
        'User';

    protected static ?string $pluralModelLabel =
        'Users';

    protected static ?int $navigationSort = 1;

    /*
    |--------------------------------------------------------------------------
    | Record Title
    |--------------------------------------------------------------------------
    */

    protected static ?string $recordTitleAttribute = 'display_name';

    /*
    |--------------------------------------------------------------------------
    | Form & Table
    |--------------------------------------------------------------------------
    */

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public static function getRelations(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [

            'index'  => ListUsers::route('/'),

            'create' => CreateUser::route('/create'),

            'edit'   => EditUser::route('/{record}/edit'),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()

            ->with([

                'company',

                'branch',

                'businessUnit',

                'department',

                'section',

                'costCenter',

                'profitCenter',

                'creator',

                'updater',

                'roles',

            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Global Search
    |--------------------------------------------------------------------------
    */

    public static function getGloballySearchableAttributes(): array
    {
        return [

            'employee_no',

            'username',

            'name',

            'email',

            'phone',

            'company.company_name',

            'department.department_name',

        ];
    }

    public static function getGlobalSearchResultDetails(
        Model $record
    ): array {

        return [

            'Employee No' => $record->employee_no,

            'Username' => $record->username,

            'Email' => $record->email,

            'Company' => $record->company?->company_name,

            'Department' => $record->department?->department_name,

            'Status' => $record->is_active
                ? 'Active'
                : 'Inactive',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation Badge
    |--------------------------------------------------------------------------
    */

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::active()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::active()->count();

        return match (true) {

            $count === 0 => 'danger',

            $count <= 10 => 'warning',

            default => 'success',

        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active Users';
    }
}