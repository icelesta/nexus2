<?php

namespace App\Filament\Resources\FiscalYears\Pages;

use App\Filament\Resources\FiscalYears\FiscalYearResource;
use App\Models\FiscalYear;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateFiscalYear extends CreateRecord
{
    protected static string $resource = FiscalYearResource::class;

    /**
     * Dipanggil setelah record berhasil dibuat.
     */
    protected function afterCreate(): void
    {
        /** @var FiscalYear $record */
        $record = $this->record;

        // Hanya boleh ada satu Fiscal Year default
        if ($record->is_default) {
            FiscalYear::query()
                ->whereKeyNot($record->getKey())
                ->update([
                    'is_default' => false,
                ]);
        }

        Notification::make()
            ->title('Fiscal Year created successfully.')
            ->success()
            ->send();
    }

    /**
     * Kembali ke halaman daftar setelah berhasil membuat data.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}