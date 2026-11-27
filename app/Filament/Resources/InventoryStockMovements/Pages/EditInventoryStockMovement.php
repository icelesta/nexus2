<?php

namespace App\Filament\Resources\InventoryStockMovements\Pages;

use App\Filament\Resources\InventoryStockMovements\InventoryStockMovementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInventoryStockMovement extends EditRecord
{
    protected static string $resource = InventoryStockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
