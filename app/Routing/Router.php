<?php

declare(strict_types=1);

namespace SchoolERP\Routing;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Exceptions\ErrorHandler;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Router
 * --------------------------------------------------------------------------
 *
 * Registers and dispatches application routes.
 *
 * Responsibilities
 * ----------------
 * • Register GET routes
 * • Register POST routes
 * • Match current request
 * • Execute route callback
 * • Return 404 when no route matches
 *
 * Zero dependencies except:
 *
 * • Request
 * • Response
 * • ErrorHandler
 */
final class Router
{
    /**
     * Registered routes.
     *
     * @var array<string,array<string,callable>>
     */
    private array $routes = [];

    /**
     * Register a GET route.
     */
    public function get(
        string $uri,
        callable $action
    ): self {

        $this->routes['GET'][$uri] = $action;

        return $this;
    }

    /**
     * Register a POST route.
     */
    public function post(
        string $uri,
        callable $action
    ): self {

        $this->routes['POST'][$uri] = $action;

        return $this;
    }

    /**
     * Dispatch the current request.
     */
    public function dispatch(
        Request $request
    ): void {

        $method = $request->method();

        $path = $request->path();

        $action = $this->routes[$method][$path] ?? null;

        if ($action === null) {
            ErrorHandler::notFound();
        }

        $response = $action($request);

        if ($response instanceof Response) {
            $response->send();
            return;
        }

        Response::make((string) $response)
            ->send();
    }
}