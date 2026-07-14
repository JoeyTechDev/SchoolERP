<?php

declare(strict_types=1);

$container = require dirname(__DIR__) . '/bootstrap/app.php';

$config = $container->make(\SchoolERP\Core\Config::class);

echo '<pre>';

echo $config->get('app.name') . PHP_EOL;

echo $config->get('app.base_url') . PHP_EOL;