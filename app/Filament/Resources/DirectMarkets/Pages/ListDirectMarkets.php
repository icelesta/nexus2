<?php

declare(strict_types=1);

namespace App\Filament\Resources\DirectMarkets\Pages;

use App\Filament\Resources\DirectMarkets\DirectMarketResource;
use App\Filament\Support\Concerns\HasGlobalTransactionFilters;
use App\Models\DirectMarket;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListDirectMarkets extends ListRecords
{
    use HasGlobalTransactionFilters;

    protected static string $resource =
        DirectMarketResource::class;

    public function getGlobalTransactionStatusOptions(): array
    {
        return [
            DirectMarket::STATUS_DRAFT =>
                DirectMarket::STATUS_DRAFT,

            DirectMarket::STATUS_SUBMITTED =>
                DirectMarket::STATUS_SUBMITTED,

            DirectMarket::STATUS_APPROVED =>
                DirectMarket::STATUS_APPROVED,

            DirectMarket::STATUS_REJECTED =>
                DirectMarket::STATUS_REJECTED,

            DirectMarket::STATUS_CANCELLED =>
                DirectMarket::STATUS_CANCELLED,

            DirectMarket::STATUS_CLOSED =>
                DirectMarket::STATUS_CLOSED,
        ];
    }

	protected function getHeaderActions(): array
	{
	    return [
	        CreateAction::make()
	            ->label('Create Direct Market')
	            ->icon('heroicon-o-plus'),
	    ];
	}
    
}
