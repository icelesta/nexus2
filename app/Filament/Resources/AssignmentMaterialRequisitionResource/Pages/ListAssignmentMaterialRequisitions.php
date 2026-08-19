<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Pages;

use App\Filament\Resources\AssignmentMaterialRequisitionResource\AssignmentMaterialRequisitionResource;
use Filament\Resources\Pages\ListRecords;

class ListAssignmentMaterialRequisitions extends ListRecords
{
    protected static string $resource =
        AssignmentMaterialRequisitionResource::class;

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    |
    | AMR is generated automatically from an approved
    | Material Requisition.
    |
    | Therefore users must NOT create AMR manually
    | from this list page.
    |
    */

    protected function getHeaderActions(): array
    {
        return [];
    }
}