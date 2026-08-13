<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApprovalMasterResource\RelationManagers;

use App\Models\ApprovalMasterStep;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApprovalStepsRelationManager extends RelationManager
{
    /*
    |--------------------------------------------------------------------------
    | Relationship
    |--------------------------------------------------------------------------
    */

    protected static string $relationship = 'steps';

    protected static ?string $title = 'Approval Levels';

    protected static ?string $recordTitleAttribute = 'approval_level';

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Approval Level
                |--------------------------------------------------------------------------
                */

                TextInput::make('approval_level')
                    ->label('Level')
                    ->numeric()
                    ->required()
                    ->readOnly()
                    ->dehydrated()
                    ->default(fn (): int => $this->getNextApprovalLevel())
                    ->helperText(
                        'Approval level is generated automatically.'
                    ),

                /*
                |--------------------------------------------------------------------------
                | Approval Role
                |--------------------------------------------------------------------------
                */

                Select::make('role_id')
                    ->label('Approval Role')
                    ->relationship(
                        name: 'role',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->where('guard_name', 'web')
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->orderBy('name'),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required()
                    ->helperText(
                        'Select the role responsible for approving this level.'
                    ),

                /*
                |--------------------------------------------------------------------------
                | Required
                |--------------------------------------------------------------------------
                */

                Toggle::make('is_required')
                    ->label('Required Approval')
                    ->default(true)
                    ->required()
                    ->helperText(
                        'At least one required approval level must remain.'
                    ),

            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Hook
    |--------------------------------------------------------------------------
    */

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {
        /*
        |--------------------------------------------------------------------------
        | Always generate the next level server-side.
        |--------------------------------------------------------------------------
        */

        $data['approval_level'] = $this->getNextApprovalLevel();

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate role inside this Approval Master.
        |--------------------------------------------------------------------------
        */

        if ($this->roleAlreadyAssigned(
            (int) ($data['role_id'] ?? 0)
        )) {
            Notification::make()
                ->danger()
                ->title('Role already assigned')
                ->body(
                    'This role is already assigned to another approval level in this Approval Master.'
                )
                ->send();

            $this->haltAction();
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Update Hook
    |--------------------------------------------------------------------------
    */

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {
        $record = $this->getMountedTableActionRecord();

        if (! $record) {
            return $data;
        }

        /*
        |--------------------------------------------------------------------------
        | Approval level is immutable.
        |--------------------------------------------------------------------------
        */

        $data['approval_level'] = $record->approval_level;

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate role.
        |--------------------------------------------------------------------------
        */

        if ($this->roleAlreadyAssigned(
            (int) ($data['role_id'] ?? 0),
            (int) $record->getKey()
        )) {
            Notification::make()
                ->danger()
                ->title('Role already assigned')
                ->body(
                    'This role is already assigned to another approval level in this Approval Master.'
                )
                ->send();

            $this->haltAction();
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Validation
    |--------------------------------------------------------------------------
    */

    protected function validateDelete(
        ApprovalMasterStep $record
    ): bool {
        $owner = $this->getOwnerRecord();

        $steps = $owner
            ->steps()
            ->orderBy('approval_level')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Rule 1
        |--------------------------------------------------------------------------
        | Active Approval Master must always contain at least
        | one approval level.
        |--------------------------------------------------------------------------
        */

        $remainingSteps = $steps
            ->where('id', '!=', $record->getKey())
            ->values();

        if (
            $owner->is_active
            && $remainingSteps->isEmpty()
        ) {
            Notification::make()
                ->danger()
                ->title('Cannot delete approval level')
                ->body(
                    'An active Approval Master must contain at least one approval level.'
                )
                ->send();

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Rule 2
        |--------------------------------------------------------------------------
        | At least one required approval level must remain.
        |--------------------------------------------------------------------------
        */

        $remainingRequiredSteps = $remainingSteps
            ->where('is_required', true);

        if ($remainingRequiredSteps->isEmpty()) {
            Notification::make()
                ->danger()
                ->title('Cannot delete approval level')
                ->body(
                    'At least one required approval level must remain in the Approval Master.'
                )
                ->send();

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Rule 3
        |--------------------------------------------------------------------------
        | Do not allow a deletion that creates a level gap.
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | 1 → Supervisor
        | 2 → Manager
        | 3 → Staff
        |
        | Deleting level 2 would produce:
        |
        | 1 → Supervisor
        | 3 → Staff
        |
        | This is not allowed.
        |--------------------------------------------------------------------------
        */

        $remainingLevels = $remainingSteps
            ->pluck('approval_level')
            ->map(fn ($level): int => (int) $level)
            ->sort()
            ->values()
            ->all();

        $expectedLevels = range(
            1,
            count($remainingLevels)
        );

        if ($remainingLevels !== $expectedLevels) {
            Notification::make()
                ->danger()
                ->title('Cannot delete approval level')
                ->body(
                    'This level cannot be deleted because it would create a gap in the approval sequence.'
                )
                ->send();

            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Role Duplicate Check
    |--------------------------------------------------------------------------
    */

    protected function roleAlreadyAssigned(
        int $roleId,
        ?int $ignoreId = null
    ): bool {
        if ($roleId <= 0) {
            return true;
        }

        $query = $this
            ->getOwnerRecord()
            ->steps()
            ->where('role_id', $roleId);

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        return $query->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Next Approval Level
    |--------------------------------------------------------------------------
    */

    protected function getNextApprovalLevel(): int
    {
        return (
            (int) $this
                ->getOwnerRecord()
                ->steps()
                ->max('approval_level')
        ) + 1;
    }

    /*
    |--------------------------------------------------------------------------
    | Halt Current Action
    |--------------------------------------------------------------------------
    */

    protected function haltAction(): void
    {
        /*
        |--------------------------------------------------------------------------
        | The mounted table action will be halted by throwing no data
        | forward through the current action lifecycle.
        |--------------------------------------------------------------------------
        |
        | Validation is additionally protected by the database/business
        | checks below. This helper intentionally keeps the form hook
        | lightweight.
        |--------------------------------------------------------------------------
        */

        throw \Illuminate\Validation\ValidationException::withMessages([
            'role_id' => 'The selected role is already assigned to this Approval Master.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                /*
                |--------------------------------------------------------------------------
                | Level
                |--------------------------------------------------------------------------
                */

                TextColumn::make('approval_level')
                    ->label('Level')
                    ->sortable()
                    ->alignCenter()
                    ->weight('medium'),

                /*
                |--------------------------------------------------------------------------
                | Approval Role
                |--------------------------------------------------------------------------
                */

                TextColumn::make('role.name')
                    ->label('Approval Role')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                /*
                |--------------------------------------------------------------------------
                | Role Code
                |--------------------------------------------------------------------------
                */

                TextColumn::make('role.role_code')
                    ->label('Role Code')
                    ->searchable()
                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Required
                |--------------------------------------------------------------------------
                */

                IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | Updated
                |--------------------------------------------------------------------------
                */

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

            ])

            ->defaultSort(
                'approval_level',
                'asc'
            )

            /*
            |--------------------------------------------------------------------------
            | Header Actions
            |--------------------------------------------------------------------------
            */

            ->headerActions([
                CreateAction::make()
                    ->label('Add Approval Level')
                    ->icon(Heroicon::OutlinedPlus)
                    ->button(),
            ])

            /*
            |--------------------------------------------------------------------------
            | Record Actions
            |--------------------------------------------------------------------------
            */

            ->recordActions([

                ActionGroup::make([

                    EditAction::make(),

                    DeleteAction::make()
                        ->before(function (
                            DeleteAction $action,
                            ApprovalMasterStep $record
                        ): void {

                            /*
                            |--------------------------------------------------------------------------
                            | Validate before actual delete.
                            |--------------------------------------------------------------------------
                            */

                            if (! $this->validateDelete($record)) {
                                $action->halt();
                            }
                        }),

                ])
                    ->label('Actions')
                    ->icon(Heroicon::OutlinedEllipsisVertical)
                    ->button(),

            ])

            ->toolbarActions([]);
    }
}