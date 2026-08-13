<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChartOfAccounts\Pages;

use App\Filament\Resources\ChartOfAccounts\ChartOfAccountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChartOfAccount extends CreateRecord
{
    protected static string $resource = ChartOfAccountResource::class;

    /**
     * Prepare data before saving.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    /**
     * After record created.
     */
    protected function afterCreate(): void
    {
        $this->redirect(
            static::getResource()::getUrl('index')
        );
    }
}