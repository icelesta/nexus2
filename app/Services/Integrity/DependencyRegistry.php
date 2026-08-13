<?php

declare(strict_types=1);

namespace App\Services\Integrity;

final class DependencyRegistry
{
    /**
     * Registered dependency rules.
     *
     * @var array<class-string,array<int,DependencyRule>>
     */
    private static array $rules = [];

    /**
     * Register dependency rules.
     *
     * @param class-string $model
     * @param array<int,DependencyRule> $rules
     */
    public static function register(
        string $model,
        array $rules,
    ): void {
        self::$rules[$model] = $rules;
    }

    /**
     * Get dependency rules.
     *
     * @param class-string $model
     *
     * @return array<int,DependencyRule>
     */
    public static function get(
        string $model,
    ): array {
        return self::$rules[$model] ?? [];
    }

    /**
     * Check registry.
     */
    public static function has(
        string $model,
    ): bool {
        return isset(self::$rules[$model]);
    }

    /**
     * Remove registry.
     */
    public static function forget(
        string $model,
    ): void {
        unset(self::$rules[$model]);
    }

    /**
     * Remove all registry.
     */
    public static function clear(): void
    {
        self::$rules = [];
    }

    /**
     * Get all registered dependency.
     *
     * @return array<class-string,array<int,DependencyRule>>
     */
    public static function all(): array
    {
        return self::$rules;
    }

    /**
     * Count registered models.
     */
    public static function count(): int
    {
        return count(self::$rules);
    }
}