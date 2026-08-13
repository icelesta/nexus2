<?php

declare(strict_types=1);

namespace App\Filament\Resources\BankAccounts\Pages;

use App\Filament\Resources\BankAccounts\BankAccountResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateBankAccount extends CreateRecord
{
    protected static string $resource =
        BankAccountResource::class;

    /**
     * Prepare data before creating a new Bank Account.
     */
    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {

        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        $data['uuid'] = (string) Str::uuid();

        /*
        |--------------------------------------------------------------------------
        | Company
        |--------------------------------------------------------------------------
        */

        if (empty($data['company_id'])) {

            $data['company_id'] = Auth::user()?->company_id;

        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $data['created_by'] = Auth::id();

        $data['updated_by'] = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Boolean Normalization
        |--------------------------------------------------------------------------
        */

        $data['allow_payment'] = (bool) ($data['allow_payment'] ?? true);

        $data['allow_receipt'] = (bool) ($data['allow_receipt'] ?? true);

        $data['allow_transfer'] = (bool) ($data['allow_transfer'] ?? true);

        $data['is_default'] = (bool) ($data['is_default'] ?? false);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        /*
        |--------------------------------------------------------------------------
        | Numeric Normalization
        |--------------------------------------------------------------------------
        */

        $data['opening_balance'] = (float) ($data['opening_balance'] ?? 0);

        return $data;
    }
}