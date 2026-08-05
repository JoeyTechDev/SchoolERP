<?php

declare(strict_types=1);

namespace SchoolERP\Http;

use SchoolERP\Container\Container;
use SchoolERP\Middleware\Pipeline;
use SchoolERP\Routing\Router;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * HTTP Kernel
 * --------------------------------------------------------------------------
 *
 * The application's central request handler.
 */
final class Kernel
{
    /**
     * Service container.
     */
    private Container $container;

    /**
     * Router.
     */
    private Router $router;

    /**
     * Middleware pipeline.
     */
    private Pipeline $pipeline;

    /**
     * Global middleware.
     *
     * @var array<int,class-string>
     */
    private array $middleware = [];

    /**
     * Constructor.
     */
    public function __construct(
        Container $container,
        Router $router
    ) {
        $this->container = $container;
        $this->router = $router;

        /*
        |--------------------------------------------------------------------------
        | Resolve Pipeline from the Container
        |--------------------------------------------------------------------------
        */
        $this->pipeline = $container->make(
            Pipeline::class
        );
    }

    /**
     * Get the service container.
     */
    public function container(): Container
    {
        return $this->container;
    }

    /**
     * Register global middleware.
     *
     * @param array<int,class-string> $middleware
     */
    public function middleware(
        array $middleware
    ): self {

        $this->middleware = $middleware;

        return $this;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request
    ): Response {

        return $this->pipeline
            ->through($this->middleware)
            ->process(

                $request,

                fn (Request $request): Response
                    => $this->router->dispatch($request)

            );
    }
}