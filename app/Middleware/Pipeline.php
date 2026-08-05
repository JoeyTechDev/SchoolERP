<?php

declare(strict_types=1);

namespace SchoolERP\Middleware;

use SchoolERP\Container\Container;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Middleware Pipeline
 * --------------------------------------------------------------------------
 *
 * Sends a request through a stack of middleware.
 */
final class Pipeline
{
    /**
     * Service container.
     */
    private Container $container;

    /**
     * Registered middleware.
     *
     * @var array<int,class-string<MiddlewareInterface>>
     */
    private array $middleware = [];

    /**
     * Constructor.
     */
    public function __construct(
        Container $container
    ) {
        $this->container = $container;
    }

    /**
     * Register middleware.
     *
     * @param array<int,class-string<MiddlewareInterface>> $middleware
     */
    public function through(
        array $middleware
    ): self {

        $this->middleware = $middleware;

        return $this;
    }

    /**
     * Execute the pipeline.
     *
     * @param callable(Request): Response $destination
     */
    public function process(
        Request $request,
        callable $destination
    ): Response {

        $pipeline = array_reduce(

            array_reverse($this->middleware),

            function (
                callable $next,
                string $middleware
            ): callable {

                return function (
                    Request $request
                ) use (
                    $middleware,
                    $next
                ): Response {

                    /** @var MiddlewareInterface $instance */
                    $instance = $this->container->make(
                        $middleware
                    );

                    return $instance->handle(
                        $request,
                        $next
                    );
                };

            },

            $destination

        );

        return $pipeline($request);
    }
}