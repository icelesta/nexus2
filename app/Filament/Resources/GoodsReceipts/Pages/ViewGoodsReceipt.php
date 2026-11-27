<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts\Pages;

use App\Filament\Resources\GoodsReceipts\GoodsReceiptResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Actions\Action;


class ViewGoodsReceipt extends ViewRecord
{
    protected static string $resource = GoodsReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToList')
                ->label('Back to Receiving Records')
                ->icon('heroicon-o-arrow-left')
                ->url(
                    fn (): string =>
                        static::getResource()::getUrl('index')
                ),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                $this->getInfolistContentComponent(),
            ]);
    }
}
