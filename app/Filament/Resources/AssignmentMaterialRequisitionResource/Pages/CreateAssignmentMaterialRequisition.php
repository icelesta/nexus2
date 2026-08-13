<?php

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Pages;

use App\Filament\Resources\AssignmentMaterialRequisitionResource\AssignmentMaterialRequisitionResource;
use App\Services\Purchasing\AssignmentMaterialRequisitionService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAssignmentMaterialRequisition extends CreateRecord
{
    protected static string $resource = AssignmentMaterialRequisitionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(AssignmentMaterialRequisitionService::class)
            ->create($data);
    }
}