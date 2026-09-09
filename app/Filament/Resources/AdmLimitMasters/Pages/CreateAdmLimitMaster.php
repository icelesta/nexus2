<?php

declare(strict_types=1);

namespace App\Filament\Resources\AdmLimitMasters\Pages;

use App\Filament\Resources\AdmLimitMasters\AdmLimitMasterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdmLimitMaster extends CreateRecord
{
    protected static string $resource = AdmLimitMasterResource::class;
}