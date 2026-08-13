<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Services\Integrity\DeleteProtectionService;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

trait HasProtectedDeleteAction
{
    protected static function deleteAction(): DeleteAction
    {
        return DeleteAction::make()

            ->requiresConfirmation()

            ->before(function (
                DeleteAction $action,
                Model $record,
            ): void {

                $result = DeleteProtectionService::check(
                    $record
                );

                if ($result->isAllowed()) {
                    return;
                }

                Notification::make()
                    ->danger()
                    ->title($result->title)
                    ->body($result->notification())
                    ->persistent()
                    ->send();

                $action->cancel();

            });
    }
}