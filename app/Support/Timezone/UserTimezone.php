<?php

declare(strict_types=1);

namespace App\Support\Timezone;

use Carbon\Carbon;
use DateTimeZone;

class UserTimezone
{
    /**
     * Get the current authenticated user's timezone.
     *
     * Fallback:
     * 1. Authenticated user's timezone
     * 2. Application timezone
     * 3. UTC
     */
    public static function timezone(): string
    {
        $timezone = auth()->user()?->timezone;

        if (! filled($timezone)) {
            $timezone = config('app.timezone', 'UTC');
        }

        try {
            new DateTimeZone($timezone);

            return $timezone;
        } catch (\Throwable) {
            return 'UTC';
        }
    }

    /**
     * Get current time in the authenticated user's timezone.
     */
    public static function now(): Carbon
    {
        return now()->setTimezone(
            self::timezone()
        );
    }

    /**
     * Convert a Carbon timestamp to the authenticated user's timezone.
     */
    public static function convert(
        ?Carbon $dateTime
    ): ?Carbon {

        if (! $dateTime) {
            return null;
        }

        return $dateTime->copy()->setTimezone(
            self::timezone()
        );
    }

    /**
     * Format a timestamp using the authenticated user's timezone.
     */
    public static function format(
        ?Carbon $dateTime,
        string $format = 'd M Y H:i'
    ): string {

        $converted = self::convert($dateTime);

        return $converted
            ? $converted->format($format)
            : '-';
    }
}