<?php

declare(strict_types=1);

namespace SchoolERP\Container;

use Closure;
use ReflectionClass;
use ReflectionException;
use SchoolERP\Container\Exceptions\NotFoundException;
use ReflectionNamedType;

final class Container implements ContainerInterface
{
    /**
     * Registered bindings.
     *
     * @var array<string, Closure>
     */
    private array $bindings = [];

    /**
     * Registered shared instances.
     *
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * Registered aliases.
     *
     * @var array<string, string>
     */
    private array $aliases = [];

    /**
     * Registered service providers.
     *
     * @var array<int,\SchoolERP\Providers\ServiceProvider>
     */
    private array $providers = [];

    /**
     * Classes currently being resolved.
     *
     * @var array<int,string>
     */
    private array $resolving = [];

    public function bind(
        string $abstract,
        Closure|string|null $concrete = null
    ): void {
        if (!$concrete instanceof Closure) {
            $target = $concrete ?? $abstract;

            $concrete = fn (ContainerInterface $container) =>
                new $target();
        }

        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(
    string $abstract,
    Closure|string|null $concrete = null
    ): void {

    if (!$concrete instanceof Closure) {

        $target = $concrete ?? $abstract;

        $concrete = fn (ContainerInterface $container) =>
            new $target();
    }

    $this->bindings[$abstract] = function (
        ContainerInterface $container
    ) use (
        $abstract,
        $concrete
    ) {

        if (!isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $concrete($container);
        }

        return $this->instances[$abstract];
    };
}

    public function instance(
        string $abstract,
        object $instance
    ): void {
        $this->instances[$abstract] = $instance;
    }

    public function make(
        string $abstract
    ): mixed {

    $abstract = $this->aliases[$abstract] ?? $abstract;

    if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
    }

    if (isset($this->bindings[$abstract])) {
            return ($this->bindings[$abstract])($this);
        }

    if (class_exists($abstract)) {
            return $this->resolve($abstract);
        }

        throw new NotFoundException(
        "No binding registered for {$abstract}."
    );
    }

    public function get(
        string $abstract
    ): mixed {
        return $this->make($abstract);
    }

    public function has(
        string $abstract
    ): bool {
        return isset($this->bindings[$abstract])
            || isset($this->instances[$abstract]);
    }

    public function alias(
        string $alias,
        string $abstract
    ): void {
        $this->aliases[$alias] = $abstract;
    }

    public function clear(): void
    {
        $this->bindings = [];
        $this->instances = [];
        $this->aliases = [];
    }

/**
 * Automatically resolve a concrete class.
 */
private function resolve(
    string $class
): object {

    if (in_array($class, $this->resolving, true)) {
        throw new NotFoundException(
            'Circular dependency detected: '
            . implode(
                ' -> ',
                [...$this->resolving, $class]
            )
        );
    }

    $this->resolving[] = $class;

    try {

        $reflection = new ReflectionClass($class);

    } catch (ReflectionException) {

        array_pop($this->resolving);

        throw new NotFoundException(
            "Class {$class} does not exist."
        );
    }

    if (!$reflection->isInstantiable()) {

        array_pop($this->resolving);

        throw new NotFoundException(
            "Class {$class} is not instantiable."
        );
    }

    $constructor = $reflection->getConstructor();

    if ($constructor === null) {

        array_pop($this->resolving);

        return new $class();
    }

    $dependencies = [];

    foreach ($constructor->getParameters() as $parameter) {

        $type = $parameter->getType();

        if (!$type instanceof ReflectionNamedType) {

            array_pop($this->resolving);

            throw new NotFoundException(
                "Unable to resolve parameter \${$parameter->getName()} in {$class}."
            );
        }

        if ($type->isBuiltin()) {

            array_pop($this->resolving);

            throw new NotFoundException(
                "Cannot auto-resolve built-in parameter \${$parameter->getName()} in {$class}."
            );
        }

        $dependencies[] = $this->make(
            $type->getName()
        );
    }

    $instance = $reflection->newInstanceArgs(
        $dependencies
    );

    array_pop($this->resolving);

    return $instance;
}

/**
 * Register a service provider.
 */
public function register(
    \SchoolERP\Providers\ServiceProvider $provider
): void {

    $provider->register();

    $this->providers[] = $provider;
}

/**
 * Boot all registered providers.
 */
public function bootProviders(): void
{
    foreach ($this->providers as $provider) {
        $provider->boot();
    }
}

/**
 * Get all registered providers.
 *
 * @return array<int,\SchoolERP\Providers\ServiceProvider>
 */
public function providers(): array
{
    return $this->providers;
}

}