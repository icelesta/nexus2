<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApprovalMasterResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;

class ApprovalMastersTable
{
    public static function configure(
        Table $table
    ): Table {
        return $table
            ->columns([

                /*
                |--------------------------------------------------------------------------
                | Code
                |--------------------------------------------------------------------------
                */

                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable(),

                /*
                |--------------------------------------------------------------------------
                | Name
                |--------------------------------------------------------------------------
                */

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                /*
                |--------------------------------------------------------------------------
                | Module
                |--------------------------------------------------------------------------
                */

                TextColumn::make('module')
                    ->label('Module')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                /*
                |--------------------------------------------------------------------------
                | Approval Levels
                |--------------------------------------------------------------------------
                */

                TextColumn::make('steps_count')
                    ->label('Levels')
                    ->counts('steps')
                    ->sortable()
                    ->alignCenter(),

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),

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
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->before(function (
                            DeleteAction $action,
                            \App\Models\ApprovalMaster $record
                        ): void {

                            if ($record->is_active) {
                                Notification::make()
                                    ->danger()
                                    ->title('Cannot delete active Approval Master')
                                    ->body(
                                        'Deactivate the Approval Master before deleting it.'
                                    )
                                    ->send();

                                $action->halt();
                            }

                            if (! $record->canBeDeleted()) {
                                Notification::make()
                                    ->danger()
                                    ->title('Approval Master cannot be deleted')
                                    ->body(
                                        'This Approval Master has already been used and must be retained for audit history.'
                                    )
                                    ->send();

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