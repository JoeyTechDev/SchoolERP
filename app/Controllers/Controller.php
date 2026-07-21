<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;

/**
 * Base Controller.
 */
abstract class Controller
{
    /**
     * Render plain text.
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
}