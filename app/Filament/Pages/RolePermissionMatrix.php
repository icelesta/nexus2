<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use App\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionMatrix extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected string $view = 'filament.pages.role-permission-matrix';
    protected static ?string $title = 'Role Permission Matrix';

    public static function shouldRegisterNavigation(): bool { return false; }

    public ?int $role_id = null;
    public array $permissions = [];

    public function mount(): void
    {
        $this->form->fill(['role_id' => null, 'permissions' => []]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('role_id')
                    ->label('Role')
                    ->options(
                        Role::query()->orderBy('name')->pluck('name', 'id')->toArray()
                    )
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        if (!$state) { $this->permissions = []; return; }
                        $role = Role::find($state);
                        if (!$role) { return; }
                        $this->permissions = $role->permissions->pluck('name')->toArray();
                    }),
                Forms\Components\CheckboxList::make('permissions')
                    ->label('Permissions')
                    ->options(
                        Permission::query()->orderBy('name')->pluck('name', 'name')->toArray()
                    )
                    ->columns(3)->searchable(),
            ])->statePath('');
    }

    public function save(): void
    {
        $role = Role::find($this->role_id);
        if (!$role) {
            Notification::make()->title('Please select a role')->danger()->send();
            return;
        }
        $role->syncPermissions($this->permissions);
        Notification::make()->title('Permissions updated successfully')->success()->send();
    }
}
