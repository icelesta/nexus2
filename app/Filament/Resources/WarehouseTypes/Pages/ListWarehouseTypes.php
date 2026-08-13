<?php

declare(strict_types=1);

namespace App\Filament\Resources\WarehouseTypes\Pages;

use App\Filament\Resources\WarehouseTypes\WarehouseTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWarehouseTypes extends ListRecords
{
    protected static string $resource = WarehouseTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()
                ->label('New Warehouse Type')
                ->icon('heroicon-o-plus')
                ->color('primary'),

        ];
    }

    public function getTitle(): string
    {
        return 'Warehouse Types';
    }

    public function getSubheading(): ?string
    {
        return 'Manage warehouse type master data.';
    }
}