<?php

declare(strict_types=1);

namespace App\Services\Approval;

use App\Models\ApprovalMaster;
use App\Models\ApprovalMasterStep;
use App\Models\User;
use Illuminate\Support\Collection;
use RuntimeException;

class ApprovalMasterService
{
    /*
    |--------------------------------------------------------------------------
    | Get Active Approval Master
    |--------------------------------------------------------------------------
    */

    public function getActiveMaster(
        string $module
    ): ?ApprovalMaster {
        return ApprovalMaster::query()
            ->active()
            ->forModule($module)
            ->with([
                'steps' => fn ($query) => $query
                    ->with('role')
                    ->orderBy('approval_level'),
            ])
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Required Approval Steps
    |--------------------------------------------------------------------------
    */

    public function getSteps(
        string $module
    ): Collection {
        $master = $this->getActiveMaster($module);

        if (! $master) {
            return collect();
        }

        return $master->steps
            ->where('is_required', true)
            ->sortBy('approval_level')
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Get First Approval Step
    |--------------------------------------------------------------------------
    */

    public function getFirstStep(
        string $module
    ): ?ApprovalMasterStep {
        return $this
            ->getSteps($module)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Get Last Approval Step
    |--------------------------------------------------------------------------
    */

    public function getLastStep(
        string $module
    ): ?ApprovalMasterStep {
        return $this
            ->getSteps($module)
            ->last();
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Approvers
    |--------------------------------------------------------------------------
    */

    public function getApproversForStep(
        ApprovalMasterStep $step
    ): Collection {
        if (! $step->role) {
            return collect();
        }

        return User::query()
            ->role($step->role->name)
            ->active()
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Approval Configuration
    |--------------------------------------------------------------------------
    */

    public function validateConfiguration(
        string $module
    ): void {
        $master = $this->getActiveMaster($module);

        if (! $master) {
            throw new RuntimeException(
                "No active Approval Master configured for module [{$module}]."
            );
        }

        $steps = $master->steps
            ->where('is_required', true)
            ->sortBy('approval_level');

        if ($steps->isEmpty()) {
            throw new RuntimeException(
                "Approval Master [{$master->code}] has no required approval steps."
            );
        }

        foreach ($steps as $step) {
            if (! $step->role) {
                throw new RuntimeException(
                    "Approval level [{$step->approval_level}] on Approval Master [{$master->code}] has no valid role."
                );
            }

            $approvers = $this->getApproversForStep($step);

            if ($approvers->isEmpty()) {
                throw new RuntimeException(
                    "No active approver found for role [{$step->role->name}] on Approval Master [{$master->code}]."
                );
            }
        }
    }

    /**
     * Validate an Approval Master before activation.
     *
     * Rules:
     * - Must have at least one approval level.
     * - Must have at least one required level.
     * - Levels must be sequential.
     * - Roles must be unique.
     * - Only one active master is allowed per module.
     */
    public function validateActivation(
        \App\Models\ApprovalMaster $master
    ): void {
        $steps = $master
            ->steps()
            ->with('role')
            ->orderBy('approval_level')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Rule 1 — Minimum Approval Level
        |--------------------------------------------------------------------------
        */

        if ($steps->isEmpty()) {
            throw new \RuntimeException(
                "Approval Master [{$master->code}] cannot be activated because no approval level is configured."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rule 2 — Minimum Required Level
        |--------------------------------------------------------------------------
        */

        if (
            $steps
                ->where('is_required', true)
                ->isEmpty()
        ) {
            throw new \RuntimeException(
                "Approval Master [{$master->code}] must have at least one required approval level."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rule 3 — Sequential Levels
        |--------------------------------------------------------------------------
        */

        $levels = $steps
            ->pluck('approval_level')
            ->map(fn ($level): int => (int) $level)
            ->values()
            ->all();

        $expectedLevels = range(
            1,
            count($levels)
        );

        if ($levels !== $expectedLevels) {
            throw new \RuntimeException(
                "Approval Master [{$master->code}] contains an invalid approval level sequence."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rule 4 — Unique Roles
        |--------------------------------------------------------------------------
        */

        $roleIds = $steps
            ->pluck('role_id')
            ->filter()
            ->values()
            ->all();

        if (count($roleIds) !== count(array_unique($roleIds))) {
            throw new \RuntimeException(
                "Approval Master [{$master->code}] contains duplicate approval roles."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rule 5 — Only One Active Master Per Module
        |--------------------------------------------------------------------------
        */

        $anotherActiveMaster = \App\Models\ApprovalMaster::query()
            ->where('module', $master->module)
            ->where('is_active', true)
            ->whereKeyNot($master->getKey())
            ->first();

        if ($anotherActiveMaster) {
            throw new \RuntimeException(
                "Module [{$master->module}] already has an active Approval Master [{$anotherActiveMaster->code}]."
            );
        }
    }

}