<?php

declare(strict_types=1);

use SchoolERP\Exceptions\ErrorHandler;

require dirname(__DIR__) . '/bootstrap/app.php';

// Uncomment ONE test at a time

// ErrorHandler::notFound();

// ErrorHandler::forbidden();

// ErrorHandler::pageExpired();

// ErrorHandler::tooManyRequests();

// ErrorHandler::serviceUnavailable();

// ErrorHandler::serverError();

throw new RuntimeException('Framework test exception.');