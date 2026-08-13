<?php

declare(strict_types=1);

namespace App\ERP\Support;

/**
 * Document Summary
 *
 * Value object representing a footer summary
 * displayed by ERPDocumentItemTable.
 *
 * This class contains no calculation logic.
 */
final class DocumentSummary
{
    private function __construct(
        private readonly string $key,
        private string $label = '',
        private mixed $value = null,
        private bool $visible = true,
        private ?string $format = null,
        private ?string $icon = null,
        private ?string $cssClass = null,
    ) {
    }

    /**
     * Create summary item.
     */
    public static function make(string $key): self
    {
        return new self($key);
    }

    /**
     * Summary label.
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Summary value.
     */
    public function value(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Display format.
     *
     * Examples:
     * number
     * decimal
     * currency
     * percentage
     */
    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Summary icon.
     */
    public function icon(string $icon): self
    {
        $this->icon = $icon;

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
     * Visible state.
     */
    public function visible(bool $condition = true): self
    {
        $this->visible = $condition;

        return $this;
    }

    /**
     * Hidden state.
     */
    public function hidden(): self
    {
        $this->visible = false;

        return $this;
    }

    /**
     * Export summary configuration.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'value' => $this->value,
            'visible' => $this->visible,
            'format' => $this->format,
            'icon' => $this->icon,
            'cssClass' => $this->cssClass,
        ];
    }
}