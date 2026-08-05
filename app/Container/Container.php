<?php

declare(strict_types=1);

namespace SchoolERP\Container;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use SchoolERP\Container\Exceptions\NotFoundException;
use SchoolERP\Providers\ServiceProvider;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Dependency Injection Container
 * --------------------------------------------------------------------------
 *
 * Core service container responsible for:
 *
 * • Dependency Injection
 * • Service Resolution
 * • Singleton Management
 * • Auto Wiring
 * • Service Providers
 *
 * This class is intentionally framework-agnostic and should remain
 * lightweight, predictable and PSR-friendly.
 */
final class Container implements ContainerInterface
{
    /**
     * Registered bindings.
     *
     * @var array<string,Closure>
     */
    private array $bindings = [];

    /**
     * Shared instances.
     *
     * @var array<string,object>
     */
    private array $instances = [];

    /**
     * Aliases.
     *
     * @var array<string,string>
     */
    private array $aliases = [];

    /**
     * Registered service providers.
     *
     * @var array<int,ServiceProvider>
     */
    private array $providers = [];

    /**
     * Currently resolving classes.
     *
     * Used to detect circular dependencies.
     *
     * @var array<int,string>
     */
    private array $resolving = [];

    /**
     * Register a transient binding.
     */
    public function bind(
        string $abstract,
        Closure|string|null $concrete = null
    ): void {

        if (!$concrete instanceof Closure) {

            $target = $concrete ?? $abstract;

            $concrete = function (
        ContainerInterface $container
        ) use (
        $target
    ) {
        return $this->resolve($target);
    };
        }

        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Register a singleton.
     */
    public function singleton(
        string $abstract,
        Closure|string|null $concrete = null
    ): void {

        if (!$concrete instanceof Closure) {

            $target = $concrete ?? $abstract;

            $concrete = function (
        ContainerInterface $container
        ) use (
            $target
        ) {
        return $this->resolve($target);
        };
        }

        $this->bindings[$abstract] = function (
            ContainerInterface $container
        ) use (
            $abstract,
            $concrete
        ) {

            if (!isset($this->instances[$abstract])) {

                $this->instances[$abstract] =
                    $concrete($container);
            }

            return $this->instances[$abstract];
        };
    }

    /**
     * Register an existing object instance.
     */
    public function instance(
        string $abstract,
        object $instance
    ): void {

        $this->instances[$abstract] = $instance;
    }

    /**
     * Register an alias.
     */
    public function alias(
        string $alias,
        string $abstract
    ): void {

        $this->aliases[$alias] = $abstract;
    }

    /**
     * Determine whether a binding exists.
     */
    public function has(
        string $abstract
    ): bool {

        $abstract = $this->aliases[$abstract]
            ?? $abstract;

        return isset($this->bindings[$abstract])
            || isset($this->instances[$abstract])
            || class_exists($abstract);
    }

    /**
     * PSR-11 compatibility.
     */
    public function get(
        string $abstract
    ): mixed {

        return $this->make($abstract);
    }

    /**
     * Resolve an object from the container.
     */
    public function make(
        string $abstract
    ): mixed {

        $abstract = $this->aliases[$abstract]
            ?? $abstract;

        /*
        |--------------------------------------------------------------------------
        | Existing singleton
        |--------------------------------------------------------------------------
        */

        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        /*
        |--------------------------------------------------------------------------
        | Registered binding
        |--------------------------------------------------------------------------
        */

        if (isset($this->bindings[$abstract])) {
            return ($this->bindings[$abstract])($this);
        }

        /*
        |--------------------------------------------------------------------------
        | Auto-resolve concrete classes
        |--------------------------------------------------------------------------
        */

        if (class_exists($abstract)) {
            return $this->resolve($abstract);
        }

        throw new NotFoundException(
            "Nothing is bound for [{$abstract}]."
        );
    }

    /**
     * Automatically resolve a concrete class.
     */
    private function resolve(
        string $class
    ): object {

        /*
        |--------------------------------------------------------------------------
        | Circular dependency detection
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Reflect class
        |--------------------------------------------------------------------------
        */

        try {

            $reflection = new ReflectionClass($class);

        } catch (ReflectionException) {

            array_pop($this->resolving);

            throw new NotFoundException(
                "Class [{$class}] does not exist."
            );
        }

        if (!$reflection->isInstantiable()) {

            array_pop($this->resolving);

            throw new NotFoundException(
                "Class [{$class}] is not instantiable."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Constructor
        |--------------------------------------------------------------------------
        */

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {

            array_pop($this->resolving);

            return new $class();
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve constructor dependencies
        |--------------------------------------------------------------------------
        */

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {

            $type = $parameter->getType();

            /*
            |--------------------------------------------------------------------------
            | Untyped parameter
            |--------------------------------------------------------------------------
            */

            if (!$type instanceof ReflectionNamedType) {

                if ($parameter->isDefaultValueAvailable()) {

                    $dependencies[] =
                        $parameter->getDefaultValue();

                    continue;
                }

                array_pop($this->resolving);

                throw new NotFoundException(
                    sprintf(
                        'Cannot resolve parameter $%s of [%s].',
                        $parameter->getName(),
                        $class
                    )
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Built-in parameter
            |--------------------------------------------------------------------------
            */

            if ($type->isBuiltin()) {

                if ($parameter->isDefaultValueAvailable()) {

                    $dependencies[] =
                        $parameter->getDefaultValue();

                    continue;
                }

                if ($type->allowsNull()) {

                    $dependencies[] = null;

                    continue;
                }

                array_pop($this->resolving);

                throw new NotFoundException(
                    sprintf(
                        'Cannot auto-resolve built-in parameter $%s of [%s].',
                        $parameter->getName(),
                        $class
                    )
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Resolve object dependency
            |--------------------------------------------------------------------------
            */

            $dependencies[] = $this->make(
                $type->getName()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create instance
        |--------------------------------------------------------------------------
        */

        $instance = $reflection->newInstanceArgs(
            $dependencies
        );

        array_pop($this->resolving);

        return $instance;
    }

    /**
     * Remove every registered binding,
     * singleton, alias and provider.
     */
    public function clear(): void
    {
        $this->bindings = [];
        $this->instances = [];
        $this->aliases = [];
        $this->providers = [];
        $this->resolving = [];
    }

    /**
     * Register a service provider.
     */
    public function register(
        ServiceProvider $provider
    ): void {

        $provider->register();

        $this->providers[] = $provider;
    }

    /**
     * Boot every registered provider.
     */
    public function bootProviders(): void
    {
        foreach ($this->providers as $provider) {
            $provider->boot();
        }
    }

    /**
     * Return registered providers.
     *
     * @return array<int,ServiceProvider>
     */
    public function providers(): array
    {
        return $this->providers;
    }
}   
