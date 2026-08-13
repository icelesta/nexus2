<?php

declare(strict_types=1);

namespace App\Services\Integrity;

use Illuminate\Database\Eloquent\Model;

final class DeleteProtectionService
{
    /**
     * Check whether a record may be deleted.
     */
    public static function check(
        Model $record,
    ): IntegrityResult {

        return IntegrityManager::canDelete(
            $record
        );
    }

    /**
     * Determine if deletion is allowed.
     */
    public static function canDelete(
        Model $record,
    ): bool {

        return self::check($record)
            ->isAllowed();

    }
}