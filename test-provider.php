<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Container\Container;
use SchoolERP\Providers\AppServiceProvider;

echo "=====================================" . PHP_EOL;
echo "SchoolERP Service Provider Test" . PHP_EOL;
echo "=====================================" . PHP_EOL;
echo PHP_EOL;

$container = new Container();

/*
|--------------------------------------------------------------------------
| Register Provider
|--------------------------------------------------------------------------
*/

$container->register(
    new AppServiceProvider($container)
);

/*
|--------------------------------------------------------------------------
| Boot Providers
|--------------------------------------------------------------------------
*/

$container->bootProviders();

/*
|--------------------------------------------------------------------------
| Retrieve Registered Service
|--------------------------------------------------------------------------
*/

$framework = $container->get('framework.name');

echo "Framework Name:" . PHP_EOL;
echo $framework->name . PHP_EOL;
echo PHP_EOL;

/*
|--------------------------------------------------------------------------
| Registered Providers
|--------------------------------------------------------------------------
*/

echo "Registered Providers:" . PHP_EOL;

foreach ($container->providers() as $provider) {
    echo get_class($provider) . PHP_EOL;
}