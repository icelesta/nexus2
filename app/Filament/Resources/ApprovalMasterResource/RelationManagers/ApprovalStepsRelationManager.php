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

    public function form(
        Schema $schema
    ): Schema {

        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | APPROVAL LEVEL
                |--------------------------------------------------------------------------
                */

                TextInput::make('approval_level')
                    ->label('Level')
                    ->numeric()
                    ->required()
                    ->readOnly()
                    ->dehydrated()
                    ->default(
                        fn (): int =>
                            $this->getNextApprovalLevel()
                    )
                    ->helperText(
                        'Approval level is generated automatically.'
                    ),


                /*
                |--------------------------------------------------------------------------
                | APPROVAL ROLE
                |--------------------------------------------------------------------------
                */

                Select::make('role_id')
                    ->label('Approval Role')
                    ->relationship(
                        name: 'role',
                        titleAttribute: 'name',
                        modifyQueryUsing:
                            fn (Builder $query): Builder =>
                                $query
                                    ->where(
                                        'guard_name',
                                        'web'
                                    )
                                    ->where(
                                        'is_active',
                                        true
                                    )
                                    ->orderBy(
                                        'sort_order'
                                    )
                                    ->orderBy(
                                        'name'
                                    ),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->live()
                    ->required()
                    ->helperText(
                        'Select the role responsible for approving this level.'
                    ),


                /*
                |--------------------------------------------------------------------------
                | APPROVAL USERS
                |--------------------------------------------------------------------------
                |
                | Optional specific-user restriction.
                |
                | Empty = all active users belonging to the
                | selected approval role remain eligible.
                |
                */

                Select::make('users')
                    ->label('Approval Users')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->relationship(
                        name: 'users',
                        titleAttribute: 'name',
                        modifyQueryUsing:
                            function (
                                Builder $query,
                                callable $get
                            ): Builder {

                                $roleId = $get(
                                    'role_id'
                                );

                                return $query
                                    ->where(
                                        'users.is_active',
                                        true
                                    )
                                    ->whereHas(
                                        'roles',
                                        function (
                                            Builder $roleQuery
                                        ) use (
                                            $roleId
                                        ): void {

                                            $roleQuery
                                                ->where(
                                                    'roles.id',
                                                    $roleId
                                                )
                                                ->where(
                                                    'roles.guard_name',
                                                    'web'
                                                )
                                                ->where(
                                                    'roles.is_active',
                                                    true
                                                );
                                        }
                                    )
                                    ->orderBy(
                                        'name'
                                    );
                            },
                    )
                    ->visible(
                        fn (
                            callable $get
                        ): bool =>
                            filled(
                                $get('role_id')
                            )
                    )
                    ->helperText(
                        'Optional. If empty, all active users with the selected role may approve.'
                    ),


                /*
                |--------------------------------------------------------------------------
                | REQUIRED
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
    | CREATE HOOK
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

        $data['approval_level'] =
            $this->getNextApprovalLevel();


        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate role inside this Approval Master.
        |--------------------------------------------------------------------------
        */

        if (
            $this->roleAlreadyAssigned(
                (int) (
                    $data['role_id']
                    ?? 0
                )
            )
        ) {

            Notification::make()
                ->danger()
                ->title(
                    'Role already assigned'
                )
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
    | UPDATE HOOK
    |--------------------------------------------------------------------------
    */

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {

        $record =
            $this->getMountedTableActionRecord();


        if (! $record) {
            return $data;
        }


        /*
        |--------------------------------------------------------------------------
        | Approval level is immutable.
        |--------------------------------------------------------------------------
        */

        $data['approval_level'] =
            $record->approval_level;


        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate role.
        |--------------------------------------------------------------------------
        */

        if (
            $this->roleAlreadyAssigned(
                (int) (
                    $data['role_id']
                    ?? 0
                ),
                (int) $record->getKey()
            )
        ) {

            Notification::make()
                ->danger()
                ->title(
                    'Role already assigned'
                )
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
    | DELETE VALIDATION
    |--------------------------------------------------------------------------
    */

    protected function validateDelete(
        ApprovalMasterStep $record
    ): bool {

        $owner =
            $this->getOwnerRecord();


        $steps =
            $owner
                ->steps()
                ->orderBy(
                    'approval_level'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | Rule 1
        |--------------------------------------------------------------------------
        | Active Approval Master must always contain
        | at least one approval level.
        |--------------------------------------------------------------------------
        */

        $remainingSteps =
            $steps
                ->where(
                    'id',
                    '!=',
                    $record->getKey()
                )
                ->values();


        if (
            $owner->is_active
            && $remainingSteps->isEmpty()
        ) {

            Notification::make()
                ->danger()
                ->title(
                    'Cannot delete approval level'
                )
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
        | At least one required level must remain.
        |--------------------------------------------------------------------------
        */

        $remainingRequired =
            $remainingSteps
                ->where(
                    'is_required',
                    true
                );


        if (
            $record->is_required
            && $remainingRequired->isEmpty()
        ) {

            Notification::make()
                ->danger()
                ->title(
                    'Cannot delete approval level'
                )
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
        | Approval levels must remain sequential.
        |--------------------------------------------------------------------------
        */

        $remainingLevels =
            $remainingSteps
                ->sortBy(
                    'approval_level'
                )
                ->pluck(
                    'approval_level'
                )
                ->map(
                    fn ($level): int =>
                        (int) $level
                )
                ->values()
                ->all();


        $expectedLevels =
            $remainingLevels === []
                ? []
                : range(
                    1,
                    count(
                        $remainingLevels
                    )
                );


        if (
            $remainingLevels !==
            $expectedLevels
        ) {

            Notification::make()
                ->danger()
                ->title(
                    'Cannot delete approval level'
                )
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
    | NEXT APPROVAL LEVEL
    |--------------------------------------------------------------------------
    */

    protected function getNextApprovalLevel(): int
    {

        $owner =
            $this->getOwnerRecord();


        $maxLevel =
            $owner
                ->steps()
                ->max(
                    'approval_level'
                );


        return (
            (int) $maxLevel
        ) + 1;
    }


    /*
    |--------------------------------------------------------------------------
    | ROLE ALREADY ASSIGNED
    |--------------------------------------------------------------------------
    */

    protected function roleAlreadyAssigned(
        int $roleId,
        ?int $ignoreId = null
    ): bool {

        if ($roleId <= 0) {
            return false;
        }


        $query =
            $this->getOwnerRecord()
                ->steps()
                ->where(
                    'role_id',
                    $roleId
                );


        if ($ignoreId !== null) {

            $query->where(
                'id',
                '!=',
                $ignoreId
            );
        }


        return $query->exists();
    }


    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    public function table(
        Table $table
    ): Table {

        return $table

            /*
            |--------------------------------------------------------------------------
            | EAGER LOAD
            |--------------------------------------------------------------------------
            |
            | Prevent N+1 queries for:
            |
            | role
            | users
            |
            */

            ->modifyQueryUsing(
                fn (Builder $query): Builder =>
                    $query->with([
                        'role',
                        'users',
                    ])
            )

            ->defaultSort(
                'approval_level',
                'asc'
            )

            ->columns([

                /*
                |--------------------------------------------------------------------------
                | LEVEL
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'approval_level'
                )
                    ->label('Level')
                    ->sortable()
                    ->alignCenter(),


                /*
                |--------------------------------------------------------------------------
                | APPROVAL ROLE
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'role.name'
                )
                    ->label('Approval Role')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),


                /*
                |--------------------------------------------------------------------------
                | APPROVAL USER
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'users.name'
                )
                    ->label('Approval User')
                    ->state(
                        function (
                            ApprovalMasterStep $record
                        ): string {

                            if (
                                $record
                                    ->users
                                    ->isEmpty()
                            ) {

                                return 'All active users';
                            }


                            return $record
                                ->users
                                ->pluck('name')
                                ->filter()
                                ->unique()
                                ->implode(', ');
                        }
                    )
                    ->wrap()
                    ->searchable()
                    ->placeholder(
                        'All active users'
                    ),


                /*
                |--------------------------------------------------------------------------
                | REQUIRED
                |--------------------------------------------------------------------------
                */

                IconColumn::make(
                    'is_required'
                )
                    ->label('Required')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),


                /*
                |--------------------------------------------------------------------------
                | UPDATED
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'updated_at'
                )
                    ->label('Updated')
                    ->dateTime(
                        'd M Y H:i'
                    )
                    ->sortable(),


            ])

            /*
            |--------------------------------------------------------------------------
            | HEADER ACTIONS
            |--------------------------------------------------------------------------
            */

            ->headerActions([

                CreateAction::make()
                    ->label(
                        'Add Approval Level'
                    )
                    ->icon(
                        'heroicon-o-plus'
                    )
                    ->color(
                        'primary'
                    ),

            ])

            /*
            |--------------------------------------------------------------------------
            | RECORD ACTIONS
            |--------------------------------------------------------------------------
            */

            ->recordActions([

                ActionGroup::make([

                    /*
                    |--------------------------------------------------------------------------
                    | EDIT
                    |--------------------------------------------------------------------------
                    */

                    EditAction::make()
                        ->label('Edit'),


                    /*
                    |--------------------------------------------------------------------------
                    | DELETE
                    |--------------------------------------------------------------------------
                    */

                    DeleteAction::make()
                        ->label('Delete')
                        ->requiresConfirmation()
                        ->before(
                            function (
                                DeleteAction $action
                            ): void {

                                $record =
                                    $this->getMountedTableActionRecord();

                                if (
                                    ! $record instanceof
                                    ApprovalMasterStep
                                ) {

                                    return;
                                }


                                if (
                                    ! $this->validateDelete(
                                        $record
                                    )
                                ) {

                                    $action->halt();
                                }
                            }
                        ),

                ])
                    ->label(
                        'Actions'
                    )
                    ->button()
                    ->color(
                        'warning'
                    )
                    ->icon(
                        Heroicon::OutlinedEllipsisVertical
                    ),

            ])

            /*
            |--------------------------------------------------------------------------
            | TOOLBAR ACTIONS
            |--------------------------------------------------------------------------
            */

            ->toolbarActions([

                /*
                | No bulk action.
                |
                | Approval level deletion has custom validation.
                |
                */

            ]);
    }
}