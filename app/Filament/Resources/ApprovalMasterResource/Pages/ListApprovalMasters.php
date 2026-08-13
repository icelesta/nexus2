<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApprovalMasterResource\Pages;

use App\Filament\Resources\ApprovalMasterResource\ApprovalMasterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApprovalMasters extends ListRecords
{
    protected static string $resource = ApprovalMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Approval Master'),
                // ->icon(...) jika nanti ingin kita tambahkan icon
        ];
    }
}