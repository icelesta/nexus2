<?php

declare(strict_types=1);

namespace App\Services\Integrity;

final class DependencyRule
{
    /**
     * Related model class.
     */
    public string $model;

    /**
     * Foreign key.
     */
    public string $foreignKey;

    /**
     * Module name.
     */
    public string $module;

    /**
     * Friendly description.
     */
    public ?string $description;

    /**
     * Constructor.
     */
    public function __construct(
        string $model,
        string $foreignKey,
        string $module,
        ?string $description = null,
    ) {
        $this->model = $model;
        $this->foreignKey = $foreignKey;
        $this->module = $module;
        $this->description = $description;
    }

    /**
     * Static factory.
     */
    public static function make(
        string $model,
        string $foreignKey,
        string $module,
        ?string $description = null,
    ): self {
        return new self(
            model: $model,
            foreignKey: $foreignKey,
            module: $module,
            description: $description,
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'foreignKey' => $this->foreignKey,
            'module' => $this->module,
            'description' => $this->description,
        ];
    }
}