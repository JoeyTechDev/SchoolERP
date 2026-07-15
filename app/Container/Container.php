<?php

declare(strict_types=1);

namespace SchoolERP\Container;

use Closure;
use ReflectionClass;
use ReflectionException;
use SchoolERP\Container\Exceptions\NotFoundException;

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

        try {

        $reflection = new ReflectionClass($class);

    } catch (ReflectionException) {

        throw new NotFoundException(
            "Class {$class} does not exist."
        );

    }

    if (!$reflection->isInstantiable()) {

        throw new NotFoundException(
            "Class {$class} is not instantiable."
        );

    }

    $constructor = $reflection->getConstructor();

    if ($constructor === null) {
        return new $class();
    }

    /*
     * Constructor injection comes in the next step.
     */

    if ($constructor->getNumberOfParameters() === 0) {
        return new $class();
    }

    throw new NotFoundException(
        "Unable to automatically resolve {$class} because it has constructor dependencies."
    );
    }
}