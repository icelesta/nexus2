<?php

declare(strict_types=1);

namespace App\ERP\DTO;

/**
 * Document Item Column
 *
 * Value Object representing a single column definition
 * used by ERPDocumentItemTable.
 *
 * This class contains no UI rendering logic or business logic.
 * It is responsible only for describing a document grid column.
 */
final class DocumentItemColumn
{
    public const ALIGN_LEFT = 'left';
    public const ALIGN_CENTER = 'center';
    public const ALIGN_RIGHT = 'right';

    private function __construct(
        private readonly string $field,
        private string $label = '',
        private bool $sortable = false,
        private bool $searchable = false,
        private bool $editable = false,
        private bool $visible = true,
        private bool $required = false,
        private mixed $defaultValue = null,
        private ?string $width = null,
        private string $alignment = self::ALIGN_LEFT,
        private ?string $tooltip = null,
        private ?string $cssClass = null,
    ) {
    }

    /**
     * Create a new document column.
     */
    public static function make(string $field): self
    {
        return new self($field);
    }

    /**
     * Column label.
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Enable sorting.
     */
    public function sortable(bool $condition = true): self
    {
        $this->sortable = $condition;

        return $this;
    }

    /**
     * Enable searching.
     */
    public function searchable(bool $condition = true): self
    {
        $this->searchable = $condition;

        return $this;
    }

    /**
     * Enable inline editing.
     */
    public function editable(bool $condition = true): self
    {
        $this->editable = $condition;

        return $this;
    }

    /**
     * Mark column as required.
     */
    public function required(bool $condition = true): self
    {
        $this->required = $condition;

        return $this;
    }

    /**
     * Show or hide column.
     */
    public function visible(bool $condition = true): self
    {
        $this->visible = $condition;

        return $this;
    }

    /**
     * Hide column.
     */
    public function hidden(): self
    {
        $this->visible = false;

        return $this;
    }

    /**
     * Column width.
     */
    public function width(string $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Column tooltip.
     */
    public function tooltip(string $tooltip): self
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * CSS class.
     */
    public function cssClass(string $cssClass): self
    {
        $this->cssClass = $cssClass;

        return $this;
    }

    /**
     * Default value.
     */
    public function defaultValue(mixed $value): self
    {
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * Align left.
     */
    public function alignLeft(): self
    {
        $this->alignment = self::ALIGN_LEFT;

        return $this;
    }

    /**
     * Align center.
     */
    public function alignCenter(): self
    {
        $this->alignment = self::ALIGN_CENTER;

        return $this;
    }

    /**
     * Align right.
     */
    public function alignRight(): self
    {
        $this->alignment = self::ALIGN_RIGHT;

        return $this;
    }

    /**
     * Export column configuration.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
            'editable' => $this->editable,
            'visible' => $this->visible,
            'required' => $this->required,
            'defaultValue' => $this->defaultValue,
            'width' => $this->width,
            'alignment' => $this->alignment,
            'tooltip' => $this->tooltip,
            'cssClass' => $this->cssClass,
        ];
    }
}