<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Override;

class EditRole extends EditRecord
{
    public Collection $permissions;

    protected static string $resource = RoleResource::class;

    /**
     * Header actions.
     *
     * Standard Nexus Master Edit layout:
     *
     * Delete | Cancel | Save changes
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),

            Action::make('cancel')
                ->label('Cancel')
                ->url(
                    fn (): string => $this->getResource()::getUrl('index')
                ),

            $this->getSaveFormAction()
                ->label('Save changes')
                ->formId('form'),
        ];
    }

    /**
     * Remove the default Save / Cancel actions
     * rendered below the form.
     */
    protected function getFormActions(): array
    {
        return [];
    }

    /**
     * Redirect to the Role List after successful save.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    #[Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(
                fn (mixed $permission, string $key): bool => ! in_array(
                    $key,
                    [
                        'name',
                        'guard_name',
                        'select_all',
                        Utils::getTenantModelForeignKey(),
                    ],
                    true
                )
            )
            ->values()
            ->flatten()
            ->unique();

        if (
            Utils::isTenancyEnabled()
            && Arr::has(
                $data,
                Utils::getTenantModelForeignKey()
            )
            && filled(
                $data[Utils::getTenantModelForeignKey()]
            )
        ) {
            return Arr::only(
                $data,
                [
                    'name',
                    'guard_name',
                    Utils::getTenantModelForeignKey(),
                ]
            );
        }

        return Arr::only(
            $data,
            [
                'name',
                'guard_name',
            ]
        );
    }

    protected function afterSave(): void
    {
        $permissionModels = collect();

        $this->permissions->each(
            function (string $permission) use ($permissionModels): void {
                $permissionModels->push(
                    Utils::getPermissionModel()::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => $this->data['guard_name'],
                    ])
                );
            }
        );

        // @phpstan-ignore-next-line
        $this->record->syncPermissions($permissionModels);

        $userId = auth()->id();

        if ($userId !== null) {
            $this->record->updateQuietly([
                'updated_by' => $userId,
            ]);
        }
    }
}