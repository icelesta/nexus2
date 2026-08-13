<?php

namespace App\Filament\Resources\ProfitCenters\Pages;

use App\Filament\Resources\ProfitCenters\ProfitCenterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProfitCenter extends CreateRecord
{
    protected static string $resource = ProfitCenterResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}