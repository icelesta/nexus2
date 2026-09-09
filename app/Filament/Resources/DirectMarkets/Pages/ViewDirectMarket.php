<?php

declare(strict_types=1);

namespace App\Filament\Resources\DirectMarkets\Pages;

use App\Filament\Resources\DirectMarkets\Actions\ApproveDirectMarket;
use App\Filament\Resources\DirectMarkets\Actions\RejectDirectMarket;

use App\Filament\Resources\DirectMarkets\DirectMarketResource;
use App\Models\DirectMarket;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewDirectMarket extends ViewRecord
{
    protected static string $resource =
        DirectMarketResource::class;

    protected function getHeaderActions(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | APPROVE DIRECT MARKET
            |--------------------------------------------------------------------------
            */

            ApproveDirectMarket::make(),

            /*
            |--------------------------------------------------------------------------
            | REJECT DIRECT MARKET
            |--------------------------------------------------------------------------
            */

            RejectDirectMarket::make(),

        ];
    }

    public function content(
        Schema $schema
    ): Schema {

        return $schema
            ->columns(1)
            ->components([

                $this->getInfolistContentComponent(),

            ]);
    }
}
