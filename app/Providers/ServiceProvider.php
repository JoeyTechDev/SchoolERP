<?php

declare(strict_types=1);

namespace SchoolERP\Providers;

use SchoolERP\Container\Container;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * ServiceProvider
 * --------------------------------------------------------------------------
 *
 * Base class for all framework service providers.
 *
 * Responsibilities
 * ----------------
 * • Register services
 * • Bootstrap services
 * • Access the Container
 *
 * Every provider should extend this class.
 */
    abstract class ServiceProvider
    {
        
    /**
     * Framework container.
     */
    protected Container $container;

    /**
     * Create a provider instance.
     */
    public function __construct(
        Container $container
    ) {
        $this->container = $container;
    }

    /**
     * Register services.
     */
    abstract public function register(): void;

    /**
     * Bootstrap services.
     *
     * Override when needed.
     */
    public function boot(): void
    {
        //
    }
}