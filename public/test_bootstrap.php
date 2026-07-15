<?php

declare(strict_types=1);

use SchoolERP\Core\Config;

$container = require dirname(__DIR__) . '/bootstrap/app.php';

$config = $container->make(Config::class);

echo '<pre>';

echo "Bootstrap Loaded Successfully\n\n";

echo "Application : " . $config->get('app.name') . PHP_EOL;
echo "Environment : " . $config->get('app.environment') . PHP_EOL;
echo "Timezone    : " . $config->get('app.timezone') . PHP_EOL;
echo "Base URL    : " . $config->get('app.base_url') . PHP_EOL;