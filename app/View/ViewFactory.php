<?php

declare(strict_types=1);

namespace SchoolERP\View;

use SchoolERP\View\Layout\LayoutManager;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * View Factory
 * --------------------------------------------------------------------------
 */
final class ViewFactory
{
    /**
     * Base views directory.
     */
    private string $viewPath;

    /**
     * Layout manager.
     */
    private LayoutManager $layout;

    /**
     * Constructor.
     */
    public function __construct(string $viewPath)
    {
        $this->viewPath = rtrim(
            $viewPath,
            DIRECTORY_SEPARATOR
        );

        $this->layout = new LayoutManager();
    }

    /**
     * Create a view.
     *
     * @param array<string,mixed> $data
     */
    public function make(
        string $view,
        array $data = []
    ): View {
        return new View(
            $this->viewPath,
            $view,
            $this->layout,
            $data
        );
    }

    /**
     * Use a layout.
     */
    public function layout(
        string $layout
    ): self {
        $this->layout->setLayout($layout);

        return $this;
    }
}