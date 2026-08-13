<?php

declare(strict_types=1);

namespace App\ERP\Tables;

/**
 * ERP Document Item Table
 *
 * Base Engine for all ERP document item grids.
 *
 * Responsibility:
 * - Toolbar metadata
 * - Grid metadata
 * - Summary metadata
 * - Empty state metadata
 *
 * This class MUST NOT render UI.
 * Presentation is handled by Blade views.
 */
abstract class ERPDocumentItemTable
{
    /**
     * Factory method.
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * Configure engine.
     */
    protected function configure(): void
    {
        //
    }

    /**
     * Boot engine.
     */
    public function boot(): void
    {
        $this->configure();
    }

    /**
     * Document title.
     */
    abstract public function getTitle(): string;

    /**
     * Document description.
     */
    public function getDescription(): ?string
    {
        return null;
    }

    /**
     * Toolbar actions.
     *
     * @return array<int, mixed>
     */
    public function getToolbarActions(): array
    {
        return [];
    }

    /**
     * Grid columns.
     *
     * @return array<int, mixed>
     */
    abstract public function getColumns(): array;

    /**
     * Row actions.
     *
     * @return array<int, mixed>
     */
    public function getRowActions(): array
    {
        return [];
    }

    /**
     * Footer summary.
     *
     * @return array<int, mixed>
     */
    public function getSummary(): array
    {
        return [];
    }

    /**
     * Empty state heading.
     */
    public function getEmptyStateHeading(): string
    {
        return 'No items available.';
    }

    /**
     * Empty state description.
     */
    public function getEmptyStateDescription(): ?string
    {
        return null;
    }
}