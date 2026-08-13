<?php

declare(strict_types=1);

namespace App\ERP\DTO;

/**
 * Document Toolbar Action
 *
 * Value Object representing a toolbar action
 * used by ERPDocumentItemTable.
 */
final class DocumentToolbarAction
{
    public const COLOR_PRIMARY = 'primary';
    public const COLOR_SUCCESS = 'success';
    public const COLOR_WARNING = 'warning';
    public const COLOR_DANGER  = 'danger';
    public const COLOR_GRAY    = 'gray';

    private function __construct(
        private readonly string $name,
        private string $label = '',
        private ?string $icon = null,
        private string $color = self::COLOR_PRIMARY,
        private bool $visible = true,
        private bool $disabled = false,
        private ?string $tooltip = null,
    ) {
    }

    /**
     * Create toolbar action.
     */
    public static function make(string $name): self
    {
        return new self($name);
    }

    /**
     * Action label.
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Action icon.
     */
    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Button color.
     */
    public function color(string $color): self
    {
        $this->color = $color;

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
     * Disabled state.
     */
    public function disabled(bool $condition = true): self
    {
        $this->disabled = $condition;

        return $this;
    }

    /**
     * Tooltip.
     */
    public function tooltip(string $tooltip): self
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * Export action configuration.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'icon' => $this->icon,
            'color' => $this->color,
            'visible' => $this->visible,
            'disabled' => $this->disabled,
            'tooltip' => $this->tooltip,
        ];
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getTooltip(): ?string
    {
        return $this->tooltip;
    }

    
}