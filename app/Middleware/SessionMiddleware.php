<?php

declare(strict_types=1);

namespace SchoolERP\Middleware;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Session\SessionInterface;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Session Middleware
 * --------------------------------------------------------------------------
 *
 * Starts the session for every incoming request.
 */
final class SessionMiddleware extends Middleware
{
    /**
     * Session manager.
     */
    private SessionInterface $session;

    /**
     * Constructor.
     */
    public function __construct(
        SessionInterface $session
    ) {
        $this->session = $session;
    }

    /**
     * Handle the request.
     */
    public function handle(
        Request $request,
        callable $next
    ): Response {

        $this->session->start();

        return $next($request);
    }
}