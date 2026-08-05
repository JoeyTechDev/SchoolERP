<?php

declare(strict_types=1);

namespace SchoolERP\View\Layout;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Layout Manager
 * --------------------------------------------------------------------------
 *
 * Responsible for managing the currently active layout
 * and the rendered page content.
 */
final class LayoutManager
{
    /**
     * Active layout.
     */
    private ?string $layout = null;

    /**
     * Rendered page content.
     */
    private string $content = '';

    /**
     * Set the active layout.
     */
    public function setLayout(string $layout): void
    {
        $this->layout = trim($layout);
    }

    /**
     * Determine whether a layout has been defined.
     */
    public function hasLayout(): bool
    {
        return $this->layout !== null
            && $this->layout !== '';
    }

    /**
     * Get the active layout.
     */
    public function layout(): ?string
    {
        return $this->layout;
    }

    /**
     * Store rendered page content.
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Retrieve rendered page content.
     */
    public function content(): string
    {
        return $this->content;
    }

    /**
     * Remove the active layout.
     */
    public function clearLayout(): void
    {
        $this->layout = null;
    }

    /**
     * Remove rendered page content.
     */
    public function clearContent(): void
    {
        $this->content = '';
    }

    /**
     * Reset the manager.
     */
    public function reset(): void
    {
        $this->clearLayout();
        $this->clearContent();
    }
}