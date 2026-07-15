<?php

declare(strict_types=1);

namespace SchoolERP\Providers;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * AppServiceProvider
 * --------------------------------------------------------------------------
 *
 * Registers core framework services.
 */
final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        // Example binding

        $this->container->instance(
            'framework.name',
            (object) [
                'name' => 'SchoolERP Framework'
            ]
        );
    }

    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        // Future boot logic goes here.
    }
}