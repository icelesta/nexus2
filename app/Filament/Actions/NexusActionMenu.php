<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NexusActionMenu extends Component
{
    /**
     * URL View
     */
    public ?string $viewUrl;

    /**
     * URL Edit
     */
    public ?string $editUrl;

    /**
     * URL Delete
     */
    public ?string $deleteUrl;

    /**
     * Record ID
     */
    public mixed $recordId;

    public function __construct(
        mixed $recordId = null,
        ?string $viewUrl = null,
        ?string $editUrl = null,
        ?string $deleteUrl = null,
    ) {
        $this->recordId = $recordId;
        $this->viewUrl = $viewUrl;
        $this->editUrl = $editUrl;
        $this->deleteUrl = $deleteUrl;
    }

    public function render(): View|Closure|string
    {
        return view('components.nexus-action-menu');
    }
}