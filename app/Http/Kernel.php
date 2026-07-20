<?php

declare(strict_types=1);

namespace SchoolERP\Http;

use SchoolERP\Routing\Router;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * HTTP Kernel
 * --------------------------------------------------------------------------
 *
 * Handles every incoming HTTP request.
 */
final class Kernel
{
    /**
     * Router instance.
     */
    private Router $router;

    /**
     * Constructor.
     */
    public function __construct(
        Router $router
    ) {
        $this->router = $router;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request
    ): Response {

        return $this->router->dispatch($request);
    }
}