<?php

declare(strict_types=1);

namespace SchoolERP\Providers;

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Framework Name
        |--------------------------------------------------------------------------
        */

        $this->container->singleton(
            'framework.name',
            fn () => (object) [
                'name' => 'SchoolERP Framework',
            ]
        );

/*
|--------------------------------------------------------------------------
| Database Manager
|--------------------------------------------------------------------------
*/

$this->container->singleton(
    Database::class,
    function () {
        return new Database(
            $this->container->get(Config::class)
        );
    }
);

        /*
        |--------------------------------------------------------------------------
        | Configuration Manager
        |--------------------------------------------------------------------------
        */

        $this->container->singleton(
            Config::class,
            fn () => new Config(
                dirname(__DIR__, 2) . '/config'
            )
        );
    }

    /**
     * Boot application services.
     */
    public function boot(): void
    {
        //
    }
}