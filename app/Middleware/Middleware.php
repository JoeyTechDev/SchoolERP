<?php

declare(strict_types=1);

namespace SchoolERP\Middleware;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Base Middleware
 * --------------------------------------------------------------------------
 *
 * Provides helper methods shared by all middleware.
 */
abstract class Middleware implements MiddlewareInterface
{
    /**
     * Continue to the next middleware.
     *
     * @param callable(Request): Response $next
     */
    protected function next(
        Request $request,
        callable $next
    ): Response {

        return $next($request);
    }

    /**
     * Abort the request.
     */
    protected function abort(
        int $status = 403,
        string $message = 'Access denied.'
    ): Response {

        return Response::make(
            $message,
            $status
        );
    }

    /**
     * Redirect the client.
     */
    protected function redirect(
        string $url
    ): Response {

        return Response::redirect($url);
    }
}