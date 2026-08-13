<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\TransactionNumbering;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionNumberingObserver
{
    /**
     * Handle the "creating" event.
     */
    public function creating(TransactionNumbering $transactionNumbering): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        $transactionNumbering->uuid ??= (string) Str::orderedUuid();

        /*
        |--------------------------------------------------------------------------
        | Normalize Data
        |--------------------------------------------------------------------------
        */

        $this->normalizeAttributes($transactionNumbering);

        /*
        |--------------------------------------------------------------------------
        | Default Values
        |--------------------------------------------------------------------------
        */

        $transactionNumbering->number_separator ??= '/';

        $transactionNumbering->running_digits = max(
            1,
            (int) $transactionNumbering->running_digits
        );

        $transactionNumbering->start_number = max(
            1,
            (int) $transactionNumbering->start_number
        );

        $transactionNumbering->current_number = max(
            0,
            (int) $transactionNumbering->current_number
        );

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if (Auth::check()) {

            $transactionNumbering->created_by ??= Auth::id();

            $transactionNumbering->updated_by ??= Auth::id();
        }
    }

    /**
     * Handle the "updating" event.
     */
    public function updating(TransactionNumbering $transactionNumbering): void
    {
        /*
        |--------------------------------------------------------------------------
        | Normalize Data
        |--------------------------------------------------------------------------
        */

        $this->normalizeAttributes($transactionNumbering);

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if (Auth::check()) {
            $transactionNumbering->updated_by = Auth::id();
        }
    }

    /**
     * Handle the "deleting" event.
     */
    public function deleting(TransactionNumbering $transactionNumbering): void
    {
        if (! Auth::check()) {
            return;
        }

        $transactionNumbering->deleted_by = Auth::id();

        $transactionNumbering->saveQuietly();
    }

    /**
     * Handle the "restoring" event.
     */
    public function restoring(TransactionNumbering $transactionNumbering): void
    {
        $transactionNumbering->deleted_by = null;
    }

    /**
     * Normalize all string attributes.
     */
    protected function normalizeAttributes(
        TransactionNumbering $transactionNumbering,
    ): void {

        $transactionNumbering->module = $this->normalize(
            $transactionNumbering->module
        );

        $transactionNumbering->document_type = $this->normalize(
            $transactionNumbering->document_type
        );

        $transactionNumbering->document_name = $this->normalizeTitle(
            $transactionNumbering->document_name
        );

        $transactionNumbering->prefix = $this->normalize(
            $transactionNumbering->prefix
        );

        $transactionNumbering->suffix = $this->normalize(
            $transactionNumbering->suffix
        );
    }

    /**
     * Normalize code values (UPPERCASE).
     */
    protected function normalize(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        return strtoupper(trim($value));
    }

    /**
     * Normalize display values (Title Case).
     */
    protected function normalizeTitle(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        return Str::title(trim($value));
    }
}