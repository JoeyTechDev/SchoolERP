<?php

declare(strict_types=1);

namespace SchoolERP\View;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * View Factory
 * --------------------------------------------------------------------------
 *
 * Creates renderable View instances.
 */
final class ViewFactory
{
    /**
     * Base views directory.
     */
    private string $viewPath;

    /**
     * Constructor.
     */
    public function __construct(string $viewPath)
    {
        $this->viewPath = rtrim(
            $viewPath,
            DIRECTORY_SEPARATOR
        );
    }

    /**
     * Create a view instance.
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
            $data
        );
    }
}