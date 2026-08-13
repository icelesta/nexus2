<?php

declare(strict_types=1);

namespace App\ERP\Contracts;

/**
 * Document Item Table Interface
 *
 * Defines the contract for all ERP document item tables.
 *
 * Every ERP document that exposes an item grid must implement
 * this contract through ERPDocumentItemTable.
 *
 * Examples:
 * - Purchase Requisition
 * - Purchase Order
 * - Request for Quotation
 * - Sales Order
 * - Goods Receipt
 * - Stock Entry
 */
interface DocumentItemTableInterface
{
    /**
     * Get document title.
     */
    public function getTitle(): string;

    /**
     * Get document description.
     */
    public function getDescription(): ?string;

    /**
     * Get toolbar actions.
     *
     * @return array<int, mixed>
     */
    public function getToolbarActions(): array;

    /**
     * Get grid columns.
     *
     * @return array<int, mixed>
     */
    public function getColumns(): array;

    /**
     * Get row actions.
     *
     * @return array<int, mixed>
     */
    public function getRowActions(): array;

    /**
     * Get footer summary.
     *
     * @return array<int, mixed>
     */
    public function getSummary(): array;

    /**
     * Get empty state heading.
     */
    public function getEmptyStateHeading(): string;

    /**
     * Get empty state description.
     */
    public function getEmptyStateDescription(): ?string;
}