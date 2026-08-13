<?php

declare(strict_types=1);

namespace App\Services\Integrity;

final class IntegrityResult
{
    /**
     * Whether the requested operation is allowed.
     */
    public function __construct(
        public readonly bool $allowed,
        public readonly string $title,
        public readonly string $message,

        /**
         * Example:
         *
         * [
         *      [
         *          'module' => 'Branch',
         *          'count'  => 5,
         *      ],
         *      [
         *          'module' => 'Warehouse',
         *          'count'  => 2,
         *      ],
         * ]
         */
        public readonly array $dependencies = [],
    ) {
    }

    public static function allow(): self
    {
        return new self(
            allowed: true,
            title: 'Success',
            message: '',
        );
    }

    public static function deny(
        string $title,
        string $message,
        array $dependencies = [],
    ): self {
        return new self(
            allowed: false,
            title: $title,
            message: $message,
            dependencies: $dependencies,
        );
    }

    public function isAllowed(): bool
    {
        return $this->allowed;
    }

    public function isDenied(): bool
    {
        return ! $this->allowed;
    }

    /**
     * Build notification body.
     */
    public function notification(): string
    {
        if ($this->dependencies === []) {
            return $this->message;
        }

        $lines = [
            $this->message,
            '',
            'Still referenced by:',
            '',
        ];

        foreach ($this->dependencies as $dependency) {

            $lines[] = sprintf(
                '• %s (%d)',
                $dependency['module'],
                $dependency['count'],
            );
        }

        $lines[] = '';
        $lines[] = 'Please deactivate this record instead of deleting it.';

        return implode(PHP_EOL, $lines);
    }
}