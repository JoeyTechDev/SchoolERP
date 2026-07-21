<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use SchoolERP\View\ViewFactory;

echo "VIEW TEST\n";
echo "=========\n\n";

$factory = new ViewFactory(
    __DIR__ . '/app/Views'
);

echo $factory
    ->make('home.index')
    ->render();