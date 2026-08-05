<?php

declare(strict_types=1);

namespace SchoolERP\Middleware;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Maintenance Middleware
 * --------------------------------------------------------------------------
 *
 * Blocks all requests while the application
 * is in maintenance mode.
 */
final class MaintenanceMiddleware extends Middleware
{
    /**
     * Maintenance switch.
     *
     * Later this will come from configuration.
     */
    private bool $enabled = false;

    /**
     * Handle request.
     */
    public function handle(
        Request $request,
        callable $next
    ): Response {

        if ($this->enabled) {

            return Response::make(
                'SchoolERP is currently under maintenance.',
                503
            );
        }

        return $this->next(
            $request,
            $next
        );
    }
}