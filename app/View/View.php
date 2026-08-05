<?php

declare(strict_types=1);

namespace SchoolERP\View;

use RuntimeException;
use SchoolERP\View\Layout\LayoutManager;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * View
 * --------------------------------------------------------------------------
 *
 * Represents a renderable view.
 */
final class View
{
    /**
     * Base view directory.
     */
    private string $viewPath;

    /**
     * View name.
     */
    private string $view;

    /**
     * View data.
     *
     * @var array<string,mixed>
     */
    private array $data;

    /**
     * Layout manager.
     */
    private LayoutManager $layout;

    /**
     * Constructor.
     *
     * @param array<string,mixed> $data
     */
    public function __construct(
        string $viewPath,
        string $view,
        LayoutManager $layout,
        array $data = []
    ) {
        $this->viewPath = rtrim(
            $viewPath,
            DIRECTORY_SEPARATOR
        );

        $this->view = $view;
        $this->layout = $layout;
        $this->data = $data;
    }

    /**
     * Render the view.
     */
    public function render(): string
    {
        $file = $this->viewFile();

        if (!is_file($file)) {
            throw new RuntimeException(
                "View [{$this->view}] not found."
            );
        }

        extract(
            $this->data,
            EXTR_SKIP
        );

        ob_start();

        require $file;

        $content = (string) ob_get_clean();

        /*
        |--------------------------------------------------------------------------
        | No Layout?
        |--------------------------------------------------------------------------
        */
        if (!$this->layout->hasLayout()) {
            return $content;
        }

        /*
        |--------------------------------------------------------------------------
        | Render Layout
        |--------------------------------------------------------------------------
        */
        $this->layout->setContent($content);

        $layoutFile = $this->viewPath
            . DIRECTORY_SEPARATOR
            . 'layouts'
            . DIRECTORY_SEPARATOR
            . $this->layout->layout()
            . '.php';

        if (!is_file($layoutFile)) {
            throw new RuntimeException(
                "Layout [{$this->layout->layout()}] not found."
            );
        }

        ob_start();

        require $layoutFile;

        $output = (string) ob_get_clean();

        $this->layout->reset();

        return $output;
    }

    /**
     * Full filename.
     */
    private function viewFile(): string
    {
        return $this->viewPath
            . DIRECTORY_SEPARATOR
            . str_replace(
                '.',
                DIRECTORY_SEPARATOR,
                $this->view
            )
            . '.php';
    }
}