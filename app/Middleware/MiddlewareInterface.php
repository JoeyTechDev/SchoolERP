<?php

declare(strict_types=1);

namespace SchoolERP\Middleware;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Middleware Interface
 * --------------------------------------------------------------------------
 *
 * Every middleware must implement this interface.
 *
 * A middleware receives:
 *  - the current HTTP request
 *  - the next middleware/controller callback
 *
 * It must return a Response.
 */
interface MiddlewareInterface
{
    /**
     * Handle an incoming request.
     *
     * @param callable(Request): Response $next
     */
    public function handle(
        Request $request,
        callable $next
    ): Response;
}