<?php

declare(strict_types=1);

namespace SchoolERP\Routing;

use SchoolERP\Container\Container;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use ReflectionMethod;
use ReflectionNamedType;

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
 * Resolve controller method dependencies.
 *
 * @param array<int,string> $routeParameters
 *
 * @return array<int,mixed>
 */
private function resolveMethodDependencies(
    object $controller,
    string $method,
    Request $request,
    array $routeParameters
): array {

    $reflection = new ReflectionMethod(
        $controller,
        $method
    );

    $arguments = [];

    $routeIndex = 0;

    foreach ($reflection->getParameters() as $parameter) {

        $type = $parameter->getType();

        /*
        |--------------------------------------------------------------------------
        | Untyped parameter
        |--------------------------------------------------------------------------
        */

        if (!$type instanceof ReflectionNamedType) {

            $arguments[] =
                $routeParameters[$routeIndex++] ?? null;

            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Request dependency
        |--------------------------------------------------------------------------
        */

        if (
            !$type->isBuiltin()
            && $type->getName() === Request::class
        ) {
            $arguments[] = $request;

            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Built-in route parameters
        |--------------------------------------------------------------------------
        */

        if ($type->isBuiltin()) {

            $value =
                $routeParameters[$routeIndex++] ?? null;

            /*
            |----------------------------------------------------------------------
            | Convert route parameter according to declared type
            |----------------------------------------------------------------------
            */

            $arguments[] = match ($type->getName()) {

                'int' => (int) $value,

                'float' => (float) $value,

                'bool' => filter_var(
                    $value,
                    FILTER_VALIDATE_BOOLEAN
                ),

                'string' => (string) $value,

                default => $value,
            };

            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Class dependency
        |--------------------------------------------------------------------------
        */

        $class = $type->getName();

        $arguments[] = $this->container->make(
            $class
        );
    }

    return $arguments;
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
    |--------------------------------------------------------------------------
    | Controller Action
    |--------------------------------------------------------------------------
    */
    if (is_array($action)) {

        [$controllerClass, $method] = $action;

        $controller = $this->container->make(
            $controllerClass
        );

        $arguments = $this->resolveMethodDependencies(
            $controller,
            $method,
            $request,
            $parameters
        );

        $response = $controller->$method(
            ...$arguments
        );

    } else {

        /*
        |--------------------------------------------------------------------------
        | Closure Route
        |--------------------------------------------------------------------------
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
