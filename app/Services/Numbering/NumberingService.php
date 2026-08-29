<?php

declare(strict_types=1);

namespace App\Services\Numbering;

use App\Models\Branch;
use App\Models\Department;
use App\Models\TransactionNumbering;
use RuntimeException;
use Illuminate\Support\Facades\DB;

class NumberingService
{
    /**
     * Generate transaction document number.
     */
    public function generate(
        string $documentType,
        ?int $companyId = null,
        ?int $businessUnitId = null,
        ?int $branchId = null,
        ?int $departmentId = null,
    ): string
    {

        return DB::transaction(function () use (
            $documentType,
            $companyId,
            $businessUnitId,
            $branchId,
            $departmentId,
        ) {
            $numbering = $this->findConfiguration(
                $documentType,
                $companyId,
                $businessUnitId,
                $branchId,
                $departmentId,
            );

            $this->validateConfiguration(
                $numbering
            );

            $this->resetIfNeeded(
                $numbering
            );

            $documentNumber = $this->buildNumber(
                $numbering,
                $departmentId,
            );

            $this->incrementCounter(
                $numbering
            );

            return $documentNumber;

        });
    }

    /**
     * Find transaction numbering configuration.
     */
    protected function findConfiguration(
        string $documentType,
        ?int $companyId,
        ?int $businessUnitId,
        ?int $branchId,
        ?int $departmentId,
    ): TransactionNumbering {

        $scopes = [

            // Level 1 — Company + Business Unit + Branch + Department
            [$companyId, $businessUnitId, $branchId, $departmentId],

            // Level 2 — Company + Branch
            [$companyId, null, $branchId, null],

            // Level 3 — Company + Branch + Department
            [$companyId, null, $branchId, $departmentId],

            // Level 4 — Company + Business Unit + Branch
            [$companyId, $businessUnitId, $branchId, null],

            // Level 5 — Company + Business Unit
            [$companyId, $businessUnitId, null, null],

            // Level 6 — Company
            [$companyId, null, null, null],

            // Level 7 — Global
            [null, null, null, null],

        ];

        foreach ($scopes as [$company, $businessUnit, $branch, $department]) {

            $numbering = $this->findByScope(
                $documentType,
                $company,
                $businessUnit,
                $branch,
                $department,
            );

            if ($numbering instanceof TransactionNumbering) {
                return $numbering;
            }
        }

        throw new RuntimeException(sprintf(
            'Transaction numbering configuration for [%s] was not found.',
            $documentType,
        ));
    }

    /**
     * Find numbering by scope.
     */
    protected function findByScope(
        string $documentType,
        ?int $companyId,
        ?int $businessUnitId,
        ?int $branchId,
        ?int $departmentId,
    ): ?TransactionNumbering {

        return TransactionNumbering::query()
            ->active()
            ->when(
                $companyId !== null,
                fn ($query) => $query->where('company_id', $companyId),
                fn ($query) => $query->whereNull('company_id'),
            )
            ->when(
                $businessUnitId !== null,
                fn ($query) => $query->where('business_unit_id', $businessUnitId),
                fn ($query) => $query->whereNull('business_unit_id'),
            )
            ->when(
                $branchId !== null,
                fn ($query) => $query->where('branch_id', $branchId),
                fn ($query) => $query->whereNull('branch_id'),
            )
            ->when(
                $departmentId !== null,
                fn ($query) => $query->where('department_id', $departmentId),
                fn ($query) => $query->whereNull('department_id'),
            )

            ->documentType($documentType)
            ->lockForUpdate()
            ->first();
    }

    /**
     * Validate numbering configuration.
     */
    protected function validateConfiguration(
        TransactionNumbering $numbering,
    ): void {

        if ($numbering->running_digits < 1) {

            throw new RuntimeException(
                'Running digits must be greater than zero.'
            );

        }

        if ($numbering->start_number < 1) {

            throw new RuntimeException(
                'Start number must be greater than zero.'
            );

        }

        if ($numbering->current_number < 0) {

            throw new RuntimeException(
                'Current number cannot be negative.'
            );

        }
    }

    /**
     * Reset running number if required.
     *
     * Reset periods are evaluated using the Company timezone.
     */
    protected function resetIfNeeded(
        TransactionNumbering $numbering,
    ): void {

        if ($numbering->last_generated_at === null) {
            return;
        }

        $now = $numbering->numberingNow();

        $lastGenerated = $numbering->last_generated_at
            ->setTimezone(
                $numbering->numberingTimezone()
            );

        $shouldReset = match ($numbering->reset_type) {

            TransactionNumbering::RESET_DAILY =>
                ! $lastGenerated->isSameDay($now),

            TransactionNumbering::RESET_MONTHLY =>
                ! $lastGenerated->isSameMonth($now),

            TransactionNumbering::RESET_YEARLY =>
                ! $lastGenerated->isSameYear($now),

            default => false,

        };

        if (! $shouldReset) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Reset Counter
        |--------------------------------------------------------------------------
        */

        $numbering->current_number =
            $numbering->start_number - 1;

        $numbering->save();
    }

    /**
     * Get next running number.
     */
    protected function nextRunningNumber(
        TransactionNumbering $numbering,
    ): string {

        return str_pad(

            (string) ($numbering->current_number + 1),

            $numbering->running_digits,

            '0',

            STR_PAD_LEFT,

        );
    }

    /**
     * Build formatted document number.
     */
    protected function buildNumber(
        TransactionNumbering $numbering,
        ?int $departmentId = null,
    ): string {

        return $this->formatNumber(
            $numbering,
            $this->nextRunningNumber($numbering),
            $departmentId,
        );
    }

    /**
     * Format document number.
     *
     * Date tokens use the Company timezone.
     */
    protected function formatNumber(
        TransactionNumbering $numbering,
        string $running,
        ?int $departmentId = null,
    ): string {

        $now = $numbering->numberingNow();

        $branchCode = $numbering->branch?->branch_code ?? '';

        $departmentCode = Department::query()
            ->whereKey($departmentId)
            ->value('department_code') ?? '';

        $number = strtr(

            $numbering->format_pattern,

            [

                '{PREFIX}' => strtoupper($numbering->prefix ?? ''),

                '{SUFFIX}' => strtoupper($numbering->suffix ?? ''),

                '{BRANCH_CODE}' => strtoupper($branchCode),

                '{DEPARTMENT_CODE}' => strtoupper($departmentCode),

                '{DOCUMENT_TYPE}' => strtoupper(
                        $numbering->prefix ?? ''
                    ),

                '{YYYY}' => $now->format('Y'),

                '{YY}' => $now->format('y'),

                '{MM}' => $now->format('m'),

                '{DD}' => $now->format('d'),

                '{RUNNING}' => $running,

            ]

        );

        /*
        |--------------------------------------------------------------------------
        | Cleanup Multiple Separators
        |--------------------------------------------------------------------------
        */

        $separator = preg_quote(
            $numbering->number_separator,
            '#'
        );

        $number = preg_replace(
            '#'.$separator.'+#',
            $numbering->number_separator,
            $number
        );

        return trim(
            $number,
            $numbering->number_separator
        );
    }

    /**
     * Increment running counter.
     *
     * The stored timestamp remains UTC.
     */
    protected function incrementCounter(
        TransactionNumbering $numbering,
    ): void {

        $numbering->current_number++;

        $numbering->last_generated_at = now();

        $numbering->save();
    }
}