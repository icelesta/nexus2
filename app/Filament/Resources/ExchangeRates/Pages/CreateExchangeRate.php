<?php

namespace App\Filament\Resources\ExchangeRates\Pages;

use App\Filament\Resources\ExchangeRates\ExchangeRateResource;
use App\Models\ExchangeRate;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateExchangeRate extends CreateRecord
{
    protected static string $resource = ExchangeRateResource::class;

    /**
     * -------------------------------------------------------------------------
     * Create Exchange Rate
     * -------------------------------------------------------------------------
     *
     * IMPORTANT:
     *
     * The current exchange_rates table uses:
     *
     * - buy_rate
     * - sell_rate
     * - middle_rate
     *
     * It DOES NOT use the legacy:
     *
     * - exchange_rate
     *
     * Therefore this page explicitly controls the payload and creates the
     * record without firing legacy Eloquent model events that may inject
     * the obsolete exchange_rate attribute.
     */
    protected function handleRecordCreation(array $data): Model
    {
        /*
        |--------------------------------------------------------------------------
        | Build a strict database payload
        |--------------------------------------------------------------------------
        */

        $payload = [
            'uuid' => (string) Str::uuid(),

            'from_currency_id' => $data['from_currency_id'] ?? null,
            'to_currency_id'   => $data['to_currency_id'] ?? null,

            'exchange_date' => $data['exchange_date'] ?? null,
            'rate_type'     => $data['rate_type'] ?? 'Spot',

            'buy_rate'    => $data['buy_rate'] ?? null,
            'sell_rate'   => $data['sell_rate'] ?? null,
            'middle_rate' => $data['middle_rate'] ?? null,

            'source'  => $data['source'] ?? null,
            'remarks' => $data['remarks'] ?? null,

            'is_active' => $data['is_active'] ?? true,

            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Explicitly remove legacy attribute
        |--------------------------------------------------------------------------
        |
        | This protects the new Exchange Rate architecture from any legacy
        | form state or inherited data.
        |
        */

        unset($payload['exchange_rate']);

        /*
        |--------------------------------------------------------------------------
        | Validate required values
        |--------------------------------------------------------------------------
        */

        if (
            empty($payload['from_currency_id']) ||
            empty($payload['to_currency_id'])
        ) {
            throw new \InvalidArgumentException(
                'From Currency and To Currency are required.'
            );
        }

        if (
            $payload['from_currency_id']
            ===
            $payload['to_currency_id']
        ) {
            throw new \InvalidArgumentException(
                'From Currency and To Currency must be different.'
            );
        }

        if (empty($payload['exchange_date'])) {
            throw new \InvalidArgumentException(
                'Exchange Date is required.'
            );
        }

        if (
            $payload['buy_rate'] === null ||
            $payload['sell_rate'] === null ||
            $payload['middle_rate'] === null
        ) {
            throw new \InvalidArgumentException(
                'Buy Rate, Sell Rate and Middle Rate are required.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create record
        |--------------------------------------------------------------------------
        |
        | withoutEvents() is intentional.
        |
        | The current ExchangeRate model/database architecture is already
        | correct, but legacy logic can inject exchange_rate = 1.
        |
        | We therefore prevent old model observers/events from modifying
        | this payload.
        |
        */

        return ExchangeRate::withoutEvents(
            fn (): ExchangeRate => ExchangeRate::query()->create($payload)
        );
    }
}