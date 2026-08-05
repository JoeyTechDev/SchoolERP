<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Response;
use SchoolERP\View\ViewFactory;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Base Controller
 * --------------------------------------------------------------------------
 *
 * Base class for all controllers.
 */
abstract class Controller
{
    /**
     * View Factory.
     */
    private ViewFactory $views;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->views = new ViewFactory(
            dirname(__DIR__, 2)
            . '/app/views'
        );
    }

    /**
     * Render a view.
     *
     * @param array<string,mixed> $data
     */
    protected function view(
        string $view,
        array $data = []
    ): Response {

        $html = $this->views
            ->layout('app')
            ->make($view, $data)
            ->render();

        return Response::make($html);
    }

    /**
     * Return JSON.
     */
    protected function json(
        array $data,
        int $status = 200
    ): Response {

        return Response::json(
            $data,
            $status
        );
    }

    /**
     * Redirect.
     */
    protected function redirect(
        string $url
    ): Response {

        return Response::redirect($url);
    }

    /**
     * Plain response.
     */
    protected function response(
        string $content,
        int $status = 200
    ): Response {

        return Response::make(
            $content,
            $status
        );
    }
}