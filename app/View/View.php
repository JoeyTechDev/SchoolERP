<?php

declare(strict_types=1);

namespace SchoolERP\View;

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
     * Constructor.
     *
     * @param array<string,mixed> $data
     */
    public function __construct(
        string $viewPath,
        string $view,
        array $data = []
    ) {
        $this->viewPath = rtrim($viewPath, DIRECTORY_SEPARATOR);
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Render the view.
     */
    public function render(): string
    {
        $file = $this->viewFile();

        if (!is_file($file)) {
            throw new \RuntimeException(
                "View [{$this->view}] not found."
            );
        }

        extract(
            $this->data,
            EXTR_SKIP
        );

        ob_start();

        require $file;

        return (string) ob_get_clean();
    }

    /**
     * Get the full view filename.
     */
    private function viewFile(): string
    {
        return $this->viewPath
            . DIRECTORY_SEPARATOR
            . str_replace('.', DIRECTORY_SEPARATOR, $this->view)
            . '.php';
    }
}