<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Response;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Base Controller
 * --------------------------------------------------------------------------
 *
 * Parent controller for all application controllers.
 */
abstract class Controller
{
    /**
     * Render a view.
     *
     * @param array<string,mixed> $data
     */
    protected function view(
        string $view,
        array $data = [],
        int $status = 200
    ): Response {

        return Response::make(
            view($view, $data),
            $status
        );
    }

    /**
     * Return JSON.
     *
     * @param array<string,mixed> $data
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
     * Plain text response.
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