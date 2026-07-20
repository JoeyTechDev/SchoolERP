<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Container\Container;

echo "CONTAINER TEST\n";
echo "==============\n\n";

$container = new Container();

/*
|--------------------------------------------------------------------------
| Bind Test
|--------------------------------------------------------------------------
*/

class Logger
{
    public function name(): string
    {
        return 'Logger';
    }
}

$container->bind(Logger::class);

$logger = $container->make(Logger::class);

echo "Bind Test: ";

echo $logger instanceof Logger
    ? "PASSED\n"
    : "FAILED\n";

/*
|--------------------------------------------------------------------------
| Singleton Test
|--------------------------------------------------------------------------
*/

class Config
{
    public string $name = 'SchoolERP';
}

$container->singleton(Config::class);

$configA = $container->make(Config::class);

$configB = $container->make(Config::class);

echo "Singleton Test: ";

echo $configA === $configB
    ? "PASSED\n"
    : "FAILED\n";

/*
|--------------------------------------------------------------------------
| Auto Resolution Test
|--------------------------------------------------------------------------
*/

class Mailer
{
}

class UserService
{
    public function __construct(
        public Mailer $mailer
    ) {
    }
}

$userService = $container->make(UserService::class);

echo "Auto Resolve Test: ";

echo $userService->mailer instanceof Mailer
    ? "PASSED\n"
    : "FAILED\n";