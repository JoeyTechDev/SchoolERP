<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use SchoolERP\Core\Config;

$config = new Config();

$config->load(
    'app',
    require dirname(__DIR__) . '/config/app.php'
);

echo '<pre>';

echo 'Application Name : ';
echo $config->get('app.name');
echo PHP_EOL;

echo 'Base URL         : ';
echo $config->get('app.base_url');
echo PHP_EOL;

echo 'Environment      : ';
echo $config->get('app.environment');
echo PHP_EOL;

echo 'Debug            : ';
echo $config->get('app.debug') ? 'true' : 'false';
echo PHP_EOL;

echo 'Timezone         : ';
echo $config->get('app.timezone');