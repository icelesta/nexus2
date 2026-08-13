<?php

declare(strict_types=1);

namespace App\Services\Integrity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

final class DependencyChecker
{
    /**
     * Check model dependency.
     */
    public static function check(Model $record): IntegrityResult
    {
        $config = config('integrity');

        $modelClass = $record::class;

        if (! isset($config[$modelClass])) {
            return IntegrityResult::allow();
        }

        $dependencies = [];

        foreach ($config[$modelClass]['dependencies'] as $dependency) {

            /** @var class-string<Model> $relatedModel */
            $relatedModel = $dependency['model'];

            $foreignKey = $dependency['foreign_key'];

            $table = (new $relatedModel())->getTable();

            /*
            |--------------------------------------------------------------------------
            | Skip if table does not contain configured foreign key
            |--------------------------------------------------------------------------
            */

            if (! Schema::hasColumn($table, $foreignKey)) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Count references
            |--------------------------------------------------------------------------
            */

            $query = $relatedModel::query()
                ->where($foreignKey, $record->getKey());

            /*
            |--------------------------------------------------------------------------
            | Respect SoftDeletes
            |--------------------------------------------------------------------------
            */

            $instance = new $relatedModel();

            if (in_array(
                \Illuminate\Database\Eloquent\SoftDeletes::class,
                class_uses_recursive($instance),
                true,
            )) {
                $query->whereNull(
                    $instance->getDeletedAtColumn()
                );
            }

            $count = $query->count();

            if ($count === 0) {
                continue;
            }

            $dependencies[] = [

                'module' => $dependency['module'],

                'count'  => $count,

            ];
        }

        if ($dependencies === []) {
            return IntegrityResult::allow();
        }

        return IntegrityResult::deny(

            title: 'Delete Failed',

            message: 'This record is already used by another module.',

            dependencies: $dependencies,

        );
    }

    /**
     * Determine whether the record has dependency.
     */
    public static function hasDependency(
        Model $record,
    ): bool {

        return self::check($record)
            ->isDenied();

    }

    /**
     * Get dependency information.
     */
    public static function dependencies(
        Model $record,
    ): array {

        return self::check($record)
            ->dependencies;

    }
}