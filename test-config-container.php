<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Config\Config;
use SchoolERP\Container\Container;
use SchoolERP\Providers\AppServiceProvider;

echo "=====================================" . PHP_EOL;
echo "Container Config Test" . PHP_EOL;
echo "=====================================" . PHP_EOL;
echo PHP_EOL;

$container = new Container();

$provider = new AppServiceProvider($container);

$provider->register();

$config = $container->get(Config::class);

echo "App Name:" . PHP_EOL;
echo $config->get('app.name') . PHP_EOL;
echo PHP_EOL;

echo "Database:" . PHP_EOL;
echo $config->get('database.mysql.database') . PHP_EOL;
echo PHP_EOL;

echo "Timezone:" . PHP_EOL;
echo $config->get('app.timezone') . PHP_EOL;