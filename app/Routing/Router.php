<?php

declare(strict_types=1);

namespace SchoolERP\Routing;

use SchoolERP\Exceptions\ErrorHandler;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;

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

        $route = $this->matchRoute(
        $method,
        $path
    );

    if ($route === null) {
        ErrorHandler::notFound();
    }

    $this->executeRoute(
    $route['action'],
    $request,
    $route['parameters']
    );
    }

    /**
     * Attempt to match a route.
     *
     * @return array{
     *     action: callable,
     *     parameters: array<int,string>
     * }|null
     */
    private function matchRoute(
        string $method,
        string $path
    ): ?array {

    foreach ($this->routes[$method] ?? [] as $route => $action) {

        $pattern = preg_replace(
            '#\{[^/]+\}#',
            '([^/]+)',
            $route
        );

        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $path, $matches)) {
            continue;
        }

        array_shift($matches);

        return [
            'action' => $action,
            'parameters' => $matches,
        ];
    }

        return null;
    }

    /**
     * Execute a matched route.
     */
    private function executeRoute(
        callable $action,
        Request $request,
        array $parameters
    ): void {

    $response = $action(
        $request,
        ...$parameters
    );

    if ($response instanceof Response) {
        $response->send();
        return;
    }

    Response::make((string) $response)
        ->send();
    } 


}