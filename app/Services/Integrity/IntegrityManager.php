<?php

declare(strict_types=1);

namespace App\Services\Integrity;

use Illuminate\Database\Eloquent\Model;

final class IntegrityManager
{
    /**
     * Validate delete operation.
     */
    public static function canDelete(
        Model $record,
    ): IntegrityResult {

        return DependencyChecker::check(
            $record
        );
    }

    /**
     * Determine if delete is allowed.
     */
    public static function isDeleteAllowed(
        Model $record,
    ): bool {

        return self::canDelete($record)
            ->isAllowed();
    }

    /**
     * Validate deactivate operation.
     */
    public static function canDeactivate(
        Model $record,
    ): IntegrityResult {

        return IntegrityResult::allow();
    }

    /**
     * Validate archive operation.
     */
    public static function canArchive(
        Model $record,
    ): IntegrityResult {

        return IntegrityResult::allow();
    }

    /**
     * Validate restore operation.
     */
    public static function canRestore(
        Model $record,
    ): IntegrityResult {

        return IntegrityResult::allow();
    }

    /**
     * Validate update operation.
     */
    public static function canUpdate(
        Model $record,
    ): IntegrityResult {

        return IntegrityResult::allow();
    }

    /**
     * Validate create operation.
     */
    public static function canCreate(
        string $model,
    ): IntegrityResult {

        return IntegrityResult::allow();
    }
}