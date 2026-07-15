<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use SchoolERP\Exceptions\ErrorHandler;

ErrorHandler::registerGlobalHandlers();

throw new RuntimeException('Framework test exception');