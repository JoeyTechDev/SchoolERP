<?php

declare(strict_types=1);

namespace SchoolERP\Http;

use SchoolERP\Container\Container;
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
     * Service Container.
     */
    private Container $container;

    /**
     * Router instance.
     */
    private Router $router;

    /**
     * Constructor.
     */
    public function __construct(
        Container $container,
        Router $router
    ) {
        $this->container = $container;
        $this->router = $router;
    }

    /**
     * Get the service container.
     */
    public function container(): Container
    {
        return $this->container;
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