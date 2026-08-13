<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items\Pages;

use App\Filament\Resources\Items\ItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()
                ->label('New Item')
                ->icon('heroicon-o-plus'),

        ];
    }

    public function getTitle(): string
    {
        return 'Item Master';
    }

    public function getHeading(): string
    {
        return 'Item Master';
    }

    public function getSubheading(): ?string
    {
        return 'Manage inventory items, suppliers, prices, stock, images, specifications, serial numbers, batches, and purchasing information.';
    }

    public function getBreadcrumb(): string
    {
        return 'Item Master';
    }
}