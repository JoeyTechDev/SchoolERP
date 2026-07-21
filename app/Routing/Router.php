<?php

declare(strict_types=1);

namespace SchoolERP\Routing;

use SchoolERP\Container\Container;
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

/****
 * Service Container.
 */
private Container $container;
/**
 * Registered routes.
 *
 * @var array<
 *     string,
 *     array<
 *         string,
 *         callable|array{class-string,string}
 *     >
 * >
 */
    private array $routes = [];

/**
 * Create a Router.
 */
public function __construct(
    Container $container
) {
    $this->container = $container;
}
    /**
     * Register a GET route.
     */
    public function get(
        string $uri,
        callable|array $action
    ): self {
        $this->routes['GET'][$uri] = $action;

        return $this;
    }

    /**
     * Register a POST route.
     */
    public function post(
        string $uri,
        callable|array $action
    ): self {
        $this->routes['POST'][$uri] = $action;

        return $this;
    }

    /**
     * Dispatch the current request.
     */
    public function dispatch(
        Request $request
    ): Response {
        $method = $request->method();

        $path = $request->path();

        $route = $this->matchRoute(
        $method,
        $path
    );

    if ($route === null) {
        return Response::notFound();
    }

    return $this->executeRoute(
        $route['action'],
        $request,
        $route['parameters']
    );
    
    }

    /**
     * Attempt to match a route.
     *
     * @return array{
     *     action: callable|array{class-string,string},
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
        callable|array $action,
        Request $request,
        array $parameters
    ): Response {

    /*
     * Controller action:
     * [Controller::class, 'method']
     */
    if (is_array($action)) {

        [$controllerClass, $method] = $action;

        $controller = $this->container->make(
            $controllerClass
        );

        $response = $controller->$method(
            $request,
            ...$parameters
        );

    } else {

        /*
         * Closure route.
         */
        $response = $action(
            $request,
            ...$parameters
        );
    }

    if ($response instanceof Response) {
        return $response;
    }

    return Response::make(
        (string) $response
    );
 
    }
}
