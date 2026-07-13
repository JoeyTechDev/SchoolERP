<?php

declare(strict_types=1);

namespace SchoolERP\Container;

use Closure;

interface ContainerInterface
{
    /**
     * Register a transient binding.
     */
    public function bind(
        string $abstract,
        Closure|string|null $concrete = null
    ): void;

    /**
     * Register a singleton binding.
     */
    public function singleton(
        string $abstract,
        Closure|string|null $concrete = null
    ): void;

    /**
     * Register an existing instance.
     */
    public function instance(
        string $abstract,
        object $instance
    ): void;

    /**
     * Resolve a service.
     */
    public function make(
        string $abstract
    ): mixed;

    /**
     * Alias of make().
     */
    public function get(
        string $abstract
    ): mixed;

    /**
     * Determine whether a binding exists.
     */
    public function has(
        string $abstract
    ): bool;

    /**
     * Register an alias.
     */
    public function alias(
        string $alias,
        string $abstract
    ): void;

    /**
     * Clear the container.
     */
    public function clear(): void;
}